<?php

namespace Adnduweb\Ci4_ecommerce\Controllers\Front;

use CodeIgniter\API\ResponseTrait;
use Adnduweb\Ci4_ecommerce\Entities\Cart;
use Adnduweb\Ci4_ecommerce\Entities\Order;
use Adnduweb\Ci4_ecommerce\Entities\Carrier;
use Adnduweb\Ci4_ecommerce\Entities\Customer;
use Adnduweb\Ci4_ecommerce\Models\ProductModel;
use Adnduweb\Ci4_ecommerce\Models\CartModel;
use Adnduweb\Ci4_ecommerce\Models\OrderModel;
use Adnduweb\Ci4_ecommerce\Models\CarriersModel;
use Adnduweb\Ci4_ecommerce\Models\CustomerModel;
use Adnduweb\Ci4_page\Libraries\PageDefault;
use Adnduweb\Ci4_ecommerce\Exceptions\EcommerceException;

class FrontShoppingCartController extends \App\Controllers\Front\FrontController
{
    use \App\Traits\BuilderModelTrait;
    use \App\Traits\ModuleTrait;

    public $name_module = 'ecommerce';

    protected $auth;
    /**
     * @var Authcustomer
     */
    protected $config;

    /**
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    /**
     * @var \CodeIgniter\Session\Session
     */
    protected $cart;


    public function __construct()
    {
        parent::__construct();
        $this->config = config('Authcustomer');
        $this->Authcustomer = service('authenticationcustomer');
        $this->cart = cart();
    }
    public function index()
    {
        //Silent
    }

    public function ajax()
    {
        if ($this->request->isAJAX()) {


            $product = (new ProductModel())->find($this->request->getPost('id_product'));
            // print_r($product); exit;

            if (!$product) {
                throw EcommerceException::forProductNotFound();
                //abort(404);
                //return $this->response->setJSON($data);
            }

            // if cart is empty then this the first product
            if (!$this->cart->contents()) {

                $this->cart->insert(array(
                    'id'      => $product->getIdItem(),
                    'qty'     => $this->request->getPost('qty'),
                    'price'   => $product->getBPrice(),
                    'name'    => $product->getBName(),
                ));


                $response = [
                    'success' => true,
                    'id'      => 123,
                    'message' => lang('Front_default.Product added to cart successfully!')
                ];
                return $this->response->setJSON($response);
            }


            // if cart not empty then check if this product exist then increment quantity
            if (!empty($this->cart->contents())) {

                foreach ($this->cart->contents() as $items) {
                    if ($items['id'] == $this->request->getPost('id_product')) {
                        $data = array(
                            "rowid" => $items["rowid"],
                            "qty" => $this->request->getPost('qty'),
                        );

                        $this->cart->update($data);

                        $response = [
                            'success' => true,
                            'id'      => 123,
                            'message' => lang('Front_default.Product added to cart successfully!')
                        ];
                              return $this->response->setJSON($response);
                    }else{
                        $this->cart->insert(array(
                            'id'      => $product->getIdItem(),
                            'qty'     => $this->request->getPost('qty'),
                            'price'   => $product->getBPrice(),
                            'name'    => $product->getBName(),
                            'options' => array('Size' => 'L', 'Color' => 'Red')
                        ));
            
                        $response = [
                            'success' => true,
                            'id'      => 123,
                            'message' => lang('Front_default.Product added to cart successfully!')
                        ];
                        return $this->response->setJSON($response);
                    }
                }
            }

            // if item not exist in cart then add to cart with quantity = 1
            $this->cart->insert(array(
                'id'      => $product->getIdItem(),
                'qty'     => $this->request->getPost('qty'),
                'price'   => $product->getBPrice(),
                'name'    => $product->getBName(),
                'options' => array('Size' => 'L', 'Color' => 'Red')
            ));

            $response = [
                'success' => true,
                'id'      => 123,
                'message' => lang('Front_default.Product added to cart successfully! encore nouveau')
            ];
            return $this->response->setJSON($response);
        }
    }


    public function ShoppingCart()
    {

        // $client = new \Google_Client();
        // print_r($client);

        if ($this->Authcustomer->check()) {
            $redirectURL = session('redirect_url') ?? '/';
            unset($_SESSION['redirect_url']);

            return redirect()->to($redirectURL);
        }

        // Set a return URL if none is specified
        $_SESSION['redirect_url'] = session('redirect_url') ?? previous_url() ?? '/';

        // Load Header
        $header_parameter = array(
            'title' => lang('Front_default.signin_title'),
            'meta_title' => lang('Front_default.signin_meta_title'),
            'meta_description' => lang('Front_default.signin_meta_description'),
            'url' => [1 => ['slug' => 'signin'], 2 => ['slug' => 'signin']],
        );
        $this->data['page'] = new PageDefault($header_parameter);
        $this->data['no_follow_no_index'] = 'index follow';
        $this->data['id']  = 'authentification';
        $this->data['class'] = $this->data['class'] . ' authentification';
        $this->data['meta_title'] = '';
        $this->data['meta_description'] = '';
        $this->data['config'] = $this->config;




        /**** CART  */


        // Call the cart service using the helper function
        $cart = cart();


        // Insert an array of values

        // $cart->insert(array(
        //     'id'      => 'sku_1234ABCD',
        //     'qty'     => 1,
        //     'price'   => '19.56',
        //     'name'    => 'T-Shirt',
        //     'options' => array('Size' => 'L', 'Color' => 'Red')
        // ));



        // // Update an array of values
        // $cart->update(array(
        //     'rowid'   => '4166b0e7fc8446e81e16s883e9a812db8',
        //     'id'     => 'sku_1234ABCD',
        //     'qty'     => 3,
        //     'price'   => '24.89',
        //     'name'    => 'T-Shirt',
        //     'options' => array('Size' => 'L', 'Color' => 'Red')
        // ));

        // // Get the total items
        // $cart->totalItems();

        // // // Remove an item using its `rowid`
        // // $cart->remove('4166b0e7fc8446e81e16883e9a812db8');

        // // // Clear the shopping cart
        // // $cart->destroy();

        // // Get the cart contents as an array
        // $cart->contents();



        return view($this->get_current_theme_view('__template_part/ecommerce/shopping_cart', 'default'), $this->data);
    }

    public function postProcessSignIn()
    {

        // validate
        $customer = new CustomerModel();
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];
        if ($this->config->validFields == ['email']) {
            $rules['login'] .= '|valid_email';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Determine credential type
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to log them in...
        if (!$this->Authcustomer->attempt([$type => $login, 'password' => $password], $remember)) {
            return redirect()->back()->withInput()->with('error', $this->Authcustomer->error() ?? lang('Authcustomercustomercustomer.badAttempt'));
        }

        $redirectURL = session('redirect_url') ?? '/my-account';
        unset($_SESSION['redirect_url']);

        return redirect()->to($redirectURL)->withCookies()->with('message', lang('Authcontroller.loginSuccess'));

        exit;
    }


    public function SignUp()
    {

        // Load Header
        $header_parameter = array(
            'title' => lang('Front_default.signup_title'),
            'meta_title' => lang('Front_default.signup_meta_title'),
            'meta_description' => lang('Front_default.signup_meta_description'),
            'url' => [1 => ['slug' => 'signup'], 2 => ['slug' => 'signup']],
        );
        $this->data['page'] = new PageDefault($header_parameter);

        $this->data['no_follow_no_index'] = 'index follow';
        $this->data['id']  = 'authentification';
        $this->data['class'] = $this->data['class'] . ' authentification';
        $this->data['meta_title'] = '';
        $this->data['meta_description'] = '';
        $this->data['config'] = $this->config;

        // check if already logged in.
        if ($this->Authcustomer->check()) {
            return redirect()->back();
        }

        // Check if registration is allowed
        if (!$this->config->allowRegistration) {
            return redirect()->back()->withInput()->with('error', lang('Authcustomercustomercustomer.registerDisabled'));
        }
        $this->data['config'] = $this->config;
        return view($this->get_current_theme_view('__template_part/ecommerce/sign_up', 'default'), $this->data);
    }

    public function postProcessSignUp()
    {


        // Check if registration is allowed
        if (!$this->config->allowRegistration) {
            return redirect()->back()->withInput()->with('error', lang('Authcustomercustomercustomer.registerDisabled'));
        }

        $customers = model('CustomerModel');

        // Validate here first, since some things,
        // like the password, can only be validated properly here.
        $rules = [
            'lastname'     => 'required|min_length[3]',
            'firstname'    => 'required|min_length[3]',
            'email'        => 'required|valid_email|is_unique[authf_customer.email]',
            'password'     => 'required|strong_password',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', service('validation')->getErrors());
        }

        // Save the user
        $allowedPostFields = array_merge(['password'], $this->config->validFields, $this->config->personalFields);
        $allowedPostFields = $this->request->getPost($allowedPostFields);
        $customer = new Customer($allowedPostFields);
        service('uuid')->uuid4()->toString();

        $this->config->requireActivation !== false ? $customer->generateActivateHash() : $customer->activate();

        // Ensure default group gets assigned if set
        if (!empty($this->config->defaultCustomerGroup)) {
            $customers = $customers->withGroup($this->config->defaultCustomerGroup);
        }

        $customer->uuid = service('uuid')->uuid4()->toString();


        if (!$customers->save($customer)) {
            //Error
            return redirect()->back()->withInput()->with('errors', $customers->errors());
        }

        if ($this->config->requireActivation !== false) {
            $activator = service('activatorCustomer');
            $template = 'front/themes/' . $this->data['theme'] . '/emails/' . $this->data['lang_iso'] . '/activation';
            $sent = $activator->send($customer, $template);

            if (!$sent) {
                return redirect()->back()->withInput()->with('error', $activator->error() ?? lang('Authcustomercustomer.unknownError'));
            }

            // Success!
            return redirect()->to($this->data['locale_route'] . 'signin')->with('message', lang('Authcustomercustomer.activationSuccess'));
        }

        // Success!
        return redirect()->to($this->data['locale_route'] . 'signin')->with('message', lang('Authcustomercustomer.registerSuccess'));
    }

    /**
     * Activate account.
     *
     * @return mixed
     */
    public function activateAccount()
    {
        ////http://startci44.lan/activate-account-customer?token=76b7d5ca33d6b2bc0cee3e11e58cab7a
        $customers = model('CustomerModel');

        // First things first - log the activation attempt.
        $customers->logActivationAttempt(
            $this->request->getGet('token'),
            $this->request->getIPAddress(),
            (string) $this->request->getUserAgent()
        );

        $throttler = service('throttler');

        if ($throttler->check($this->request->getIPAddress(), 2, MINUTE) === false) {
            return service('response')->setStatusCode(429)->setBody(lang('Authcustomer.tooManyRequests', [$throttler->getTokentime()]));
        }

        $customer = $customers->where('activate_hash', $this->request->getGet('token'))
            ->where('active', 0)
            ->first();

        if (is_null($customer)) {
            return redirect()->route('signin')->with('error', lang('Authcustomer.activationNoUser'));
        }

        $customer->activate();

        $customers->save($customer);

        return redirect()->route('signin')->with('message', lang('Authcustomer.registerSuccess'));
    }

    public function loginSocialGoogle()
    {


        if (isset($_GET['code'])) {

            // Authenticate user with google
            if ($this->google->getAuthenticate()) {

                // Get user info from google
                $gpInfo = $this->google->getUserInfo();

                // Preparing data for database insertion
                $userData['oauth_provider'] = 'google';
                $userData['oauth_uid']         = $gpInfo['id'];
                $userData['first_name']     = $gpInfo['given_name'];
                $userData['last_name']         = $gpInfo['family_name'];
                $userData['email']             = $gpInfo['email'];
                $userData['gender']         = !empty($gpInfo['gender']) ? $gpInfo['gender'] : '';
                $userData['locale']         = !empty($gpInfo['locale']) ? $gpInfo['locale'] : '';
                $userData['picture']         = !empty($gpInfo['picture']) ? $gpInfo['picture'] : '';

                // Insert or update user data to the database
                $userID = $this->user->checkUser($userData);

                // Store the status and user profile info into session
                $this->session->set_userdata('loggedIn', true);
                $this->session->set_userdata('userData', $userData);

                // Redirect to profile page
                redirect('user_authentication/profile/');
            }
        }

        // Google authentication url
        $data['loginURL'] = $this->google->loginURL();

        // Load google login view
        $this->load->view('user_authentication/index', $data);
    }

    public function profileGoogle()
    {
        // Redirect to login page if the user not logged in
        if (!$this->session->userdata('loggedIn')) {
            redirect('/user_authentication/');
        }

        // Get user info from session
        $data['userData'] = $this->session->userdata('userData');

        // Load user profile view
        $this->load->view('user_authentication/profile', $data);
    }

    public function logoutSocial()
    {
        // Reset OAuth access token
        $this->google->revokeToken();

        // Remove token and user data from the session
        $this->session->unset_userdata('loggedIn');
        $this->session->unset_userdata('userData');

        // Destroy entire session data
        $this->session->sess_destroy();

        // Redirect to login page
        redirect('/user_authentication/');
    }
}
