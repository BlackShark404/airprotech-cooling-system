<?php

namespace App\Controllers;

use App\Models\BaseModel;

/**
 * DataTableController - A specialized controller for handling DataTables server-side processing
 * 
 * This controller extends the BaseController and provides standardized methods to handle
 * DataTables server-side requests, making integration of DataTables in your application
 * more streamlined and consistent.
 */
class DataTableController extends BaseController
{
    /**
     * @var BaseModel The model instance to use for data operations
     */
    protected $model;
    
    /**
     * @var array Columns that can be searched in the table
     */
    protected $searchableColumns = [];
    
    /**
     * @var array Column mapping from DataTables to database columns
     */
    protected $columnMap = [];
    
    /**
     * @var string Primary key column name
     */
    protected $primaryKey = 'id';
    
    /**
     * @var array Additional filters to apply to all queries
     */
    protected $fixedFilters = [];
    
    /**
     * @var array Custom column formatters
     */
    protected $columnFormatters = [];

    /**
     * Constructor
     * 
     * @param string|null $modelName Name of the model to load
     */
    public function __construct(string $modelName)
    {
        parent::__construct();
        
        if ($modelName) {
            $this->model = $this->loadModel($modelName);
            if (method_exists($this->model, 'getSearchableFields')) {
                $searchableFields = $this->model->getSearchableFields();
                if (!empty($searchableFields)) {
                    $this->searchableColumns = $searchableFields;
                }
            }
        }
    }
    
    /**
     * Set the model to use
     * 
     * @param BaseModel $model Model instance
     * @return $this
     */
    public function setModel(BaseModel $model)
    {
        $this->model = $model;
        return $this;
    }
    
    /**
     * Set searchable columns
     * 
     * @param array $columns Columns that can be searched
     * @return $this
     */
    public function setSearchableColumns(array $columns)
    {
        $this->searchableColumns = $columns;
        return $this;
    }
    
    /**
     * Set column mapping
     * 
     * @param array $map Associative array mapping DataTables columns to DB columns
     * @return $this
     */
    public function setColumnMap(array $map)
    {
        $this->columnMap = $map;
        return $this;
    }
    
    /**
     * Set fixed filters that will be applied to all queries
     * 
     * @param array $filters Array of conditions and values
     * @return $this
     */
    public function setFixedFilters(array $filters)
    {
        $this->fixedFilters = $filters;
        return $this;
    }
    
    /**
     * Add a column formatter function
     * 
     * @param string $column Column name
     * @param callable $formatter Function that formats the column value
     * @return $this
     */
    public function addColumnFormatter(string $column, callable $formatter)
    {
        $this->columnFormatters[$column] = $formatter;
        return $this;
    }
    
    /**
     * Handle DataTables server-side processing request
     * 
     * This method processes the DataTables AJAX request parameters and returns
     * the appropriate JSON response with data, filtered count, and total count.
     * 
     * @return void Outputs JSON response
     */
    public function handleRequest()
    {
        // Check if this is an AJAX request
        if (!$this->isAjax()) {
            $this->jsonError('Invalid request', 400);
        }
        
        // Get request data from POST or GET
        $request = $this->request();
        
        try {
            // Get draw parameter
            $draw = isset($request['draw']) ? intval($request['draw']) : 1;
            
            // Build query with filters
            $query = $this->buildQuery($request);
            
            // Get total count before filtering
            $totalRecords = $this->model->count();
            
            // Get filtered count
            $recordsFiltered = $query->count();
            
            // Apply pagination and ordering
            $this->applyPagination($query, $request);
            $this->applyOrdering($query, $request);
            
            // Execute query and get results
            $data = $query->get();
            
            // Format data for DataTables
            $formattedData = $this->formatData($data);
            
            // Return success response
            $this->json([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            $this->jsonError('Error processing request: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Build the base query with filters
     * 
     * @param array $request Request parameters
     * @return BaseModel Query builder instance
     */
    protected function buildQuery(array $request)
    {
        // Clone the model to avoid modifying the original
        $query = clone $this->model;
        
        // Apply search filters
        $this->applySearchFilters($query, $request);
        
        // Apply fixed filters
        $this->applyFixedFilters($query);
        
        return $query;
    }
    
    /**
     * Apply search filters to the query
     * 
     * @param BaseModel $query Query builder instance
     * @param array $request Request parameters
     * @return void
     */
    protected function applySearchFilters(BaseModel $query, array $request)
    {
        // Global search
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            
            if (!empty($this->searchableColumns)) {
                $query->whereGroup();
                
                foreach ($this->searchableColumns as $index => $column) {
                    if ($index > 0) {
                        $query->orWhere();
                    }
                    $query->whereLike($column, "%{$searchValue}%");
                }
                
                $query->endWhereGroup();
            }
        }
        
        // Column-specific filtering
        if (isset($request['columns']) && is_array($request['columns'])) {
            foreach ($request['columns'] as $columnIndex => $column) {
                if (!empty($column['search']['value'])) {
                    $columnName = $this->getColumnName($columnIndex, $request);
                    if ($columnName) {
                        $query->whereLike($columnName, "%{$column['search']['value']}%");
                    }
                }
            }
        }
    }
    
    /**
     * Apply fixed filters to the query
     * 
     * @param BaseModel $query Query builder instance
     * @return void
     */
    protected function applyFixedFilters(BaseModel $query)
    {
        foreach ($this->fixedFilters as $column => $value) {
            if (is_array($value)) {
                if (isset($value['operator'])) {
                    // Custom operator filter
                    $this->applyOperatorFilter($query, $column, $value['operator'], $value['value']);
                } else {
                    // IN filter
                    $query->whereIn($column, $value);
                }
            } else {
                // Equality filter
                $query->whereEqual($column, $value);
            }
        }
    }
    
    /**
     * Apply a filter with a custom operator
     * 
     * @param BaseModel $query Query builder instance
     * @param string $column Column name
     * @param string $operator Operator (>, <, >=, <=, !=, LIKE, etc.)
     * @param mixed $value Filter value
     * @return void
     */
    protected function applyOperatorFilter(BaseModel $query, string $column, string $operator, $value)
    {
        $operator = strtoupper($operator);
        
        switch ($operator) {
            case '>':
                $query->whereGreaterThan($column, $value);
                break;
            case '>=':
                $query->whereGreaterThanOrEqual($column, $value);
                break;
            case '<':
                $query->whereLessThan($column, $value);
                break;
            case '<=':
                $query->whereLessThanOrEqual($column, $value);
                break;
            case '!=':
                $query->whereNotEqual($column, $value);
                break;
            case 'LIKE':
                $query->whereLike($column, $value);
                break;
            case 'NOT LIKE':
                $query->whereNotLike($column, $value);
                break;
            case 'NULL':
                $query->whereNull($column);
                break;
            case 'NOT NULL':
                $query->whereNotNull($column);
                break;
            default:
                // For custom raw conditions
                $query->where("$column $operator :value", ['value' => $value]);
                break;
        }
    }
    
    /**
     * Apply pagination to the query
     * 
     * @param BaseModel $query Query builder instance
     * @param array $request Request parameters
     * @return void
     */
    protected function applyPagination(BaseModel $query, array $request)
    {
        if (isset($request['start']) && isset($request['length'])) {
            $start = intval($request['start']);
            $length = intval($request['length']);
            
            if ($length > 0) {
                $query->limit($length)->offset($start);
            }
        }
    }
    
    /**
     * Apply ordering to the query
     * 
     * @param BaseModel $query Query builder instance
     * @param array $request Request parameters
     * @return void
     */
    protected function applyOrdering(BaseModel $query, array $request)
    {
        if (isset($request['order']) && is_array($request['order'])) {
            $orderClauses = [];
            
            foreach ($request['order'] as $order) {
                $columnIndex = $order['column'];
                $direction = $order['dir'];
                
                $columnName = $this->getColumnName($columnIndex, $request);
                if ($columnName) {
                    $orderClauses[] = "$columnName " . strtoupper($direction);
                }
            }
            
            if (!empty($orderClauses)) {
                $query->orderBy(implode(', ', $orderClauses));
            }
        }
    }
    
    /**
     * Get the database column name for a DataTables column index
     * 
     * @param int $columnIndex Column index from DataTables
     * @param array $request Request parameters
     * @return string|null Database column name or null if not found
     */
    protected function getColumnName(int $columnIndex, array $request)
    {
        // Get the column name from the request
        $datatablesColumnName = isset($request['columns'][$columnIndex]['data']) 
            ? $request['columns'][$columnIndex]['data'] 
            : null;
        
        if ($datatablesColumnName === null) {
            return null;
        }
        
        // Check if there's a mapping for this column
        if (isset($this->columnMap[$datatablesColumnName])) {
            return $this->columnMap[$datatablesColumnName];
        }
        
        // Otherwise return the DataTables column name
        return $datatablesColumnName;
    }
    
    /**
     * Format data for DataTables response
     * 
     * @param array $data Raw data records
     * @return array Formatted data
     */
    protected function formatData(array $data)
    {
        $formattedData = [];
        
        foreach ($data as $row) {
            $formattedRow = is_array($row) ? $row : (array) $row;
            
            // Apply column formatters
            foreach ($this->columnFormatters as $column => $formatter) {
                if (isset($formattedRow[$column])) {
                    $formattedRow[$column] = call_user_func($formatter, $formattedRow[$column], $formattedRow);
                }
            }
            
            $formattedData[] = $formattedRow;
        }
        
        return $formattedData;
    }
    
    /**
     * Render a DataTable view
     * 
     * @param string $view View name
     * @param array $tableConfig Configuration for the DataTable
     * @param array $data Additional data to pass to the view
     * @return void
     */
    public function renderDataTable(string $view, array $tableConfig, array $data = [])
    {
        // Merge table configuration with data
        $viewData = array_merge($data, [
            'tableConfig' => json_encode($tableConfig)
        ]);
        
        $this->render($view, $viewData);
    }
    
    /**
     * Process a simple CRUD operation (create, update, delete)
     * 
     * @param string $operation Operation name (create, update, delete)
     * @param array $data Data for the operation
     * @param int|null $id Record ID for update/delete operations
     * @return void Outputs JSON response
     */
    public function processOperation(string $operation, array $data = [], int $id)
    {
        try {
            switch ($operation) {
                case 'create':
                    $result = $this->model->insert($data);
                    $message = 'Record created successfully';
                    break;
                    
                case 'update':
                    if ($id === null) {
                        throw new \Exception('ID is required for update operation');
                    }
                    $result = $this->model->update($data, "{$this->primaryKey} = :id", ['id' => $id]);
                    $message = 'Record updated successfully';
                    break;
                    
                case 'delete':
                    if ($id === null) {
                        throw new \Exception('ID is required for delete operation');
                    }
                    $result = $this->model->delete("{$this->primaryKey} = :id", ['id' => $id]);
                    $message = 'Record deleted successfully';
                    break;
                    
                default:
                    throw new \Exception("Unknown operation: $operation");
            }
            
            if ($result) {
                $this->jsonSuccess([], $message);
            } else {
                $this->jsonError('Operation failed');
            }
        } catch (\Exception $e) {
            $this->jsonError('Error: ' . $e->getMessage());
        }
    }
}