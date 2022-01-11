<?php

    namespace Sbmedical\Comparator\Controller\Validate;

    class Validate extends \Magento\Framework\App\Action\Action
    {
        protected $_email;
        protected $_logo;
        protected $_storeManager;
        protected $_scopeConfig;
        private $_captcha;

        public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Sbmedical\Sbcaptcha\Helper\Captcha $captcha,
            \Sbmedical\Sbreviews\Helper\Email $email,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Theme\Block\Html\Header\Logo $logo
        )
        {
            $this->_email = $email;
            $this->_logo = $logo;
            $this->_storeManager = $storeManager;
            $this->_scopeConfig = $scopeConfig;
            $this->_captcha = $captcha;
            return parent::__construct($context);
        }

        protected function store_info()
        {
            return [
                'id' => $this->_storeManager->getStore()->getId(),
                'name' => ucwords($this->_scopeConfig->getValue(
                    'trans_email/ident_sales/name',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )),
                'logo' => $this->_logo->getLogoSrc(),
                'email' => 'sales@sbmedical.com'
              //  'email' => 'contact@'.$this->_storeManager->getStore()->getName()
            ];
        }

        public function execute()
        {
            $post = $this->getRequest()->getParams();
            $store_info = $this->store_info();
            $captcha = trim($this->_captcha->verifyResponse()->success);

            if ( empty( $captcha ) || strlen( $captcha ) == 0 ) :
                print json_encode(array(
                    'status' => false,
                    'element' => false,
                    'message' => 'Please select captcha to continue.'
                ));
            elseif ($post['contact_name'] == '') :
                print json_encode(array(
                    'status' => false,
                    'element' => '#contact_name',
                    'message' => 'Contact Name is empty.'
                ));
            elseif ($post['business_name'] == '') :
                print json_encode(array(
                    'status' => false,
                    'element' => '#business_name',
                    'message' => 'Name of Business is empty.'
                ));
            elseif  ($post['business_phone'] == '') :
                print json_encode(array(
                    'status' => false,
                    'element' => '#business_phone',
                    'message' => 'Business Phone is empty.'
                ));
            elseif ($post['email'] == '') :
                print json_encode(array(
                    'status' => false,
                    'element' => '#email',
                    'message' => 'Email is empty.'
                ));
            elseif (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) :
                print json_encode(array(
                    'status' => false,
                    'element' => '#email',
                    'message' => 'Please use a well format email.'
                ));
            elseif ($post['address1'] == '') :
                print json_encode(array(
                    'status' => false,
                    'element' => '#address1',
                    'message' => 'Address 1 is empty.'
                ));
            elseif ($post['country'] == '') :
                print json_encode(array(
                    'status' => false,
                    'element' => '#country',
                    'message' => 'Country is empty.'
                ));
            elseif ($post['state'] == '') :
                print json_encode(array(
                    'status' => false,
                    'element' => '#state',
                    'message' => 'State is empty.'
                ));
            elseif ($post['city'] == '') :
                print json_encode(array(
                    'status' => false,
                    'element' => '#city',
                    'message' => 'City is empty.'
                ));
            else:
                $post['contact_name'] = ucwords(strtolower($post['contact_name']));
                $post['email'] = strtolower($post['email']);

                $mail = '<table border="0" width="600">';

                /* Logo */
                $mail .= '<tr><td colspan="2" align="center"><img src="'.$store_info['logo'].'"></td></tr>';
                $mail .= '<tr><td colspan="2">&nbsp;</td></tr>';
                $mail .= '<tr><td align="center"><table border="0" cellspacing="0" cellpadding="5" width="400">';

                /* Nombre del Contacto */
                $mail .= '<tr>';
                $mail .= '<td width="40%" style="font-weight: bold;">Contact Name</td>';
                $mail .= '<td width="60%">'.$post['contact_name'].'</td>';
                $mail .= '</tr>';

                /* Nombre del Negocio */
                $mail .= '<tr>';
                $mail .= '<td width="40%" style="font-weight: bold;">Name of Business</td>';
                $mail .= '<td width="60%">'.$post['business_name'].'</td>';
                $mail .= '</tr>';

                /* Telefono del Negocio */
                $mail .= '<tr>';
                $mail .= '<td width="40%" style="font-weight: bold;">Business Phone</td>';
                $mail .= '<td width="60%">'.$post['business_phone'].'</td>';
                $mail .= '</tr>';

                /*  Email */
                $mail .= '<tr>';
                $mail .= '<td width="40%" style="font-weight: bold;">Email</td>';
                $mail .= '<td width="60%">'.$post['email'].'</td>';
                $mail .= '</tr>';

                /* Direccion 1 del Negocio */
                $mail .= '<tr>';
                $mail .= '<td width="40%" style="font-weight: bold;">Address 1</td>';
                $mail .= '<td width="60%">'.$post['address1'].'</td>';
                $mail .= '</tr>';

                /* Direccion 2 del Negocio */
                if ($post['address2'] != ''):
                    $mail .= '<tr>';
                    $mail .= '<td width="40%" style="font-weight: bold;">Address 2</td>';
                    $mail .= '<td width="60%">'.$post['address2'].'</td>';
                    $mail .= '</tr>';
                endif;

                /* Pais */
                $mail .= '<tr>';
                $mail .= '<td width="40%" style="font-weight: bold;">Country</td>';
                $mail .= '<td width="60%">'.$post['country'].'</td>';
                $mail .= '</tr>';

                /* Estado */
                $mail .= '<tr>';
                $mail .= '<td width="40%" style="font-weight: bold;">State</td>';
                $mail .= '<td width="60%">'.$post['state'].'</td>';
                $mail .= '</tr>';

                /* Ciudad */
                $mail .= '<tr>';
                $mail .= '<td width="40%" style="font-weight: bold;">City</td>';
                $mail .= '<td width="60%">'.$post['city'].'</td>';
                $mail .= '</tr>';

                /* Zip */
                if ($post['zip'] != ''):
                    $mail .= '<tr>';
                    $mail .= '<td width="40%" style="font-weight: bold;">Zip</td>';
                    $mail .= '<td width="60%">'.$post['zip'].'</td>';
                    $mail .= '</tr>';
                endif;

                /*  Monto Estimado a Gastar */
                if ($post['m_spending'] != ''):
                    $mail .= '<tr>';
                    $mail .= '<td width="40%" style="font-weight: bold;">Estimated Monthly Spending</td>';
                    $mail .= '<td width="60%">'.$post['m_spending'].'</td>';
                    $mail .= '</tr>';
                endif;

                $mail .= '</table></td></tr>';
                $mail .= '</table>';

                $emailTempVariables = [
                    'html' => $mail,
                    'subject' => $store_info['name'].' Wholesale Application Form',
                ];
                $senderInfo = [
                    'name' => $store_info['name'],
                    'email' => $store_info['email']
                ];
                $receiverInfo = [
                    'name' => 'Admin',
                    'email' => $store_info['email']
                ];

                $this->_email->sendCustomEmail(
                    $emailTempVariables,
                    $senderInfo,
                    $receiverInfo,
                    $store_info['id']
                );

                print json_encode(array(
                    'status' => true,
                    'message' => 'Your Form was submitted. Thank you.',
                    'datas' => $post
                ));
            endif;

            return;
        }
    }
