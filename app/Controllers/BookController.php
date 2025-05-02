<?php

namespace App\Controllers;

class BookController extends BaseController
{
    protected $bookModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->bookModel = $this->loadModel('BookModel');
    }
    
    /**
     * Render the book management page
     */
    public function renderBookManagement()
    {
        $this->render('admin/book-management', [
            'title' => 'Book Management',
            'pageTitle' => 'Book Management'
        ]);
    }
    
    /**
     * Handle DataTables AJAX request for books data
     */
    public function getBooksData()
    {
        // Check if request is AJAX
        if (!$this->isAjax()) {
            $this->renderError('Invalid request', 400);
            return;
        }
        
        // Get DataTables parameters
        $draw = $this->request('draw', 1);
        $start = $this->request('start', 0);
        $length = $this->request('length', 10);
        $search = $this->request('search')['value'] ?? '';
        
        // Order column
        $orderColumnIndex = $this->request('order')[0]['column'] ?? 0;
        $orderColumns = [
            'id', 'title', 'author', 'isbn', 'category', 
            'publication_year', 'publisher', 'price', 'in_stock'
        ];
        $orderColumn = $orderColumns[$orderColumnIndex] ?? 'id';
        $orderDirection = $this->request('order')[0]['dir'] ?? 'asc';
        
        // Get books data
        $result = $this->bookModel->getDataTablesData(
            $start, 
            $length, 
            $search, 
            ['column' => $orderColumn, 'dir' => $orderDirection]
        );
        
        // Format the data for DataTables
        $formattedData = array_map(function($book) {
            // Format price with dollar sign
            $book['price'] = '$' . number_format($book['price'], 2);
            
            // Format in_stock as Yes/No
            $book['in_stock'] = $book['in_stock'] ? 'Yes' : 'No';
            
            return $book;
        }, $result['data']);
        
        // Prepare response for DataTables
        $response = [
            'draw' => (int) $draw,
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['total'],
            'data' => $formattedData
        ];
        
        $this->json($response);
    }
}