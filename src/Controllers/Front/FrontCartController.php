//https://codeigniter.com/userguide3/libraries/cart.html

// Call the cart service using the helper function

$cart = cart();



// Insert an array of values

$cart->insert(array(

'id' => 'sku_1234ABCD',

'qty' => 1,

'price' => '19.56',

'name' => 'T-Shirt',

'options' => array('Size' => 'L', 'Color' => 'Red')

));



// Update an array of values

$cart->update(array(

'rowid' => '4166b0e7fc8446e81e16883e9a812db8',

'id' => 'sku_1234ABCD',

'qty' => 3,

'price' => '24.89',

'name' => 'T-Shirt',

'options' => array('Size' => 'L', 'Color' => 'Red')

));



// Get the total items

$cart->totalItems();



// Remove an item using its `rowid`

$cart->remove('4166b0e7fc8446e81e16883e9a812db8');



// Clear the shopping cart

$cart->destroy();



// Get the cart contents as an array

$cart->contents();