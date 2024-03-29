<?php
/**
 * @package     Arastta eCommerce
 * @copyright   2015-2017 Arastta Association. All rights reserved.
 * @copyright   See CREDITS.txt for credits and other copyright notices.
 * @license     GNU GPL version 3; see LICENSE.txt
 * @link        https://arastta.org
 */

class ControllerPaymentGrezpay extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('payment/grezpay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('grezpay', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            if (isset($this->request->post['button']) and $this->request->post['button'] == 'save') {
                $this->response->redirect($this->url->link($this->request->get['route'], 'token=' . $this->session->data['token'], 'SSL'));
            }

            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit']      = $this->language->get('text_edit');
        $data['text_enabled']   = $this->language->get('text_enabled');
        $data['text_disabled']  = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['entry_merchant']     = $this->language->get('entry_merchant');
        $data['entry_security']     = $this->language->get('entry_security');
        $data['entry_callback']     = $this->language->get('entry_callback');
        $data['entry_total']        = $this->language->get('entry_total');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_geo_zone']     = $this->language->get('entry_geo_zone');
        $data['entry_status']       = $this->language->get('entry_status');
        $data['entry_sort_order']   = $this->language->get('entry_sort_order');

        $data['help_callback'] = $this->language->get('help_callback');
        $data['help_total']    = $this->language->get('help_total');

        $data['button_save']      = $this->language->get('button_save');
        $data['button_savenew']   = $this->language->get('button_savenew');
        $data['button_saveclose'] = $this->language->get('button_saveclose');
        $data['button_cancel']    = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['merchant'])) {
            $data['error_merchant'] = $this->error['merchant'];
        } else {
            $data['error_merchant'] = '';
        }

        if (isset($this->error['security'])) {
            $data['error_security'] = $this->error['security'];
        } else {
            $data['error_security'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/grezpay', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('payment/grezpay', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['grezpay_merchant'])) {
            $data['grezpay_merchant'] = $this->request->post['grezpay_merchant'];
        } else {
            $data['grezpay_merchant'] = $this->config->get('grezpay_merchant');
        }

        if (isset($this->request->post['grezpay_security'])) {
            $data['grezpay_security'] = $this->request->post['grezpay_security'];
        } else {
            $data['grezpay_security'] = $this->config->get('grezpay_security');
        }

        $data['callback'] = HTTP_CATALOG . 'index.php?route=payment/grezpay/callback';

        if (isset($this->request->post['grezpay_total'])) {
            $data['grezpay_total'] = $this->request->post['grezpay_total'];
        } else {
            $data['grezpay_total'] = $this->config->get('grezpay_total');
        }

        if (isset($this->request->post['grezpay_order_status_id'])) {
            $data['grezpay_order_status_id'] = $this->request->post['grezpay_order_status_id'];
        } else {
            $data['grezpay_order_status_id'] = $this->config->get('grezpay_order_status_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['grezpay_geo_zone_id'])) {
            $data['grezpay_geo_zone_id'] = $this->request->post['grezpay_geo_zone_id'];
        } else {
            $data['grezpay_geo_zone_id'] = $this->config->get('grezpay_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['grezpay_status'])) {
            $data['grezpay_status'] = $this->request->post['grezpay_status'];
        } else {
            $data['grezpay_status'] = $this->config->get('grezpay_status');
        }

        if (isset($this->request->post['grezpay_sort_order'])) {
            $data['grezpay_sort_order'] = $this->request->post['grezpay_sort_order'];
        } else {
            $data['grezpay_sort_order'] = $this->config->get('grezpay_sort_order');
        }

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/grezpay.tpl', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/grezpay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['grezpay_merchant']) {
            $this->error['merchant'] = $this->language->get('error_merchant');
        }

        if (!$this->request->post['grezpay_security']) {
            $this->error['security'] = $this->language->get('error_security');
        }

        return !$this->error;
    }
}
