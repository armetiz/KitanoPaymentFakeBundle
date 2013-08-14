<?php

namespace Kitano\PaymentFakeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function paymentRequestAction()
    {
        $request = $this->getRequest()->request;
        $mode = strtolower($request->get('mode', ''));
        $data = $request->all();
        switch ($mode) {
            case 'accept':
                $data['code'] = 1;
                $redirectRoute = $request->get('internal_back_to_shop_route');
                break;

            case 'refuse':
                $data['code'] = 0;
                $redirectRoute = $request->get('internal_back_to_shop_route');
                break;

            default:
                $data['code'] = -1;
                $redirectRoute = $request->get('internal_back_to_shop_route');
        }

        
        
        $this->sendPaymentNotification($data, $this->generateUrl($request->get('notification_route')));

        return $this->redirect($this->generateUrl($redirectRoute, array('transactionId' => $data["transactionId"])));
    }

    public function captureRequestAction()
    {
        $postData = $this->getRequest()->request;

        return new Response('code=1');
    }

    private function sendPaymentNotification(array $data, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        // Get the response and close the channel.
        $response = curl_exec($ch);
        curl_close($ch);
    }

}

