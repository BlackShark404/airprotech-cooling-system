/**
 * Test script to verify the inventory preservation during product updates
 * 
 * This script can be included in a test page to validate that product variants
 * maintain their inventory information after product updates.
 */

// Global variables to store products for comparison
let originalProduct = null;
let updatedProduct = null;

// Function to fetch a product by ID
async function fetchProduct(productId) {
    try {
        const response = await fetch(`/api/products/${productId}`);
        const result = await response.json();

        if (result && result.success && result.data) {
            return result.data;
        } else {
            console.error('Error fetching product:', result.message || 'Unknown error');
            return null;
        }
    } catch (error) {
        console.error('Exception fetching product:', error);
        return null;
    }
}

// Function to update a product
async function updateProduct(productId, productData) {
    try {
        const formData = new FormData();
        formData.append('product', JSON.stringify(productData.product || {}));
        formData.append('features', JSON.stringify(productData.features || []));
        formData.append('specs', JSON.stringify(productData.specs || []));
        formData.append('variants', JSON.stringify(productData.variants || []));

        const response = await fetch(`/api/products/${productId}`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result && result.success) {
            return true;
        } else {
            console.error('Error updating product:', result.message || 'Unknown error');
            return false;
        }
    } catch (error) {
        console.error('Exception updating product:', error);
        return false;
    }
}

// Function to run the test
async function testProductInventoryPreservation(productId) {
    console.group('Product Inventory Preservation Test');
    console.log(`Testing product ID: ${productId}`);

    // Step 1: Fetch the original product with inventory data
    console.log('Step 1: Fetching original product data...');
    originalProduct = await fetchProduct(productId);

    if (!originalProduct) {
        console.error('Could not fetch original product data. Test aborted.');
        console.groupEnd();
        return;
    }

    // Log original variant information
    console.log('Original product variants:');
    if (originalProduct.variants && originalProduct.variants.length > 0) {
        originalProduct.variants.forEach(variant => {
            console.log(`Variant ID ${variant.VAR_ID || variant.var_id}: ${variant.VAR_CAPACITY || variant.var_capacity} - Inventory: ${variant.INVENTORY_QUANTITY || 0}`);
        });
    } else {
        console.warn('Original product has no variants.');
    }

    // Step 2: Prepare update data (preserving all existing data)
    const updateData = {
        product: {
            PROD_NAME: originalProduct.PROD_NAME || originalProduct.prod_name,
            PROD_DESCRIPTION: originalProduct.PROD_DESCRIPTION || originalProduct.prod_description,
            PROD_AVAILABILITY_STATUS: originalProduct.PROD_AVAILABILITY_STATUS || originalProduct.prod_availability_status,
            PROD_IMAGE: originalProduct.PROD_IMAGE || originalProduct.prod_image
        },
        features: originalProduct.features || [],
        specs: originalProduct.specs || [],
        variants: originalProduct.variants || []
    };

    // Make a minor change to the product to trigger an update
    updateData.product.PROD_DESCRIPTION = (updateData.product.PROD_DESCRIPTION || '') + ' (Updated)';

    // Step 3: Update the product
    console.log('Step 3: Updating product...');
    const updateSuccess = await updateProduct(productId, updateData);

    if (!updateSuccess) {
        console.error('Failed to update product. Test aborted.');
        console.groupEnd();
        return;
    }

    // Wait a moment for the update to complete
    await new Promise(resolve => setTimeout(resolve, 1000));

    // Step 4: Fetch the updated product
    console.log('Step 4: Fetching updated product data...');
    updatedProduct = await fetchProduct(productId);

    if (!updatedProduct) {
        console.error('Could not fetch updated product data. Test incomplete.');
        console.groupEnd();
        return;
    }

    // Step 5: Compare variants and inventory data
    console.log('Updated product variants:');
    if (updatedProduct.variants && updatedProduct.variants.length > 0) {
        updatedProduct.variants.forEach(variant => {
            console.log(`Variant ID ${variant.VAR_ID || variant.var_id}: ${variant.VAR_CAPACITY || variant.var_capacity} - Inventory: ${variant.INVENTORY_QUANTITY || 0}`);
        });
    } else {
        console.warn('Updated product has no variants.');
    }

    // Compare original and updated variants
    console.log('Step 5: Comparing original and updated variants...');

    // Create maps for easy comparison
    const originalVariantsMap = {};
    const updatedVariantsMap = {};

    if (originalProduct.variants) {
        originalProduct.variants.forEach(variant => {
            const capacity = variant.VAR_CAPACITY || variant.var_capacity;
            originalVariantsMap[capacity] = variant.INVENTORY_QUANTITY || 0;
        });
    }

    if (updatedProduct.variants) {
        updatedProduct.variants.forEach(variant => {
            const capacity = variant.VAR_CAPACITY || variant.var_capacity;
            updatedVariantsMap[capacity] = variant.INVENTORY_QUANTITY || 0;
        });
    }

    // Check all original variants against updated variants
    let testPassed = true;
    for (const capacity in originalVariantsMap) {
        if (updatedVariantsMap[capacity] === undefined) {
            console.error(`Variant with capacity ${capacity} is missing after update`);
            testPassed = false;
        } else if (updatedVariantsMap[capacity] !== originalVariantsMap[capacity]) {
            console.error(`Inventory quantity mismatch for capacity ${capacity}: Original=${originalVariantsMap[capacity]}, Updated=${updatedVariantsMap[capacity]}`);
            testPassed = false;
        } else {
            console.log(`✓ Capacity ${capacity}: Inventory preserved (${originalVariantsMap[capacity]})`);
        }
    }

    // Final result
    if (testPassed) {
        console.log('✅ TEST PASSED: All variant inventory quantities were preserved');
    } else {
        console.error('❌ TEST FAILED: Some inventory quantities were lost or changed');
    }

    console.groupEnd();
    return testPassed;
}

// Export the test function
window.testProductInventoryPreservation = testProductInventoryPreservation; 