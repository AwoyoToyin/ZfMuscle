<?php

namespace ZfMuscle\Controller;

use Zend\View\Model\ViewModel;
use ZfMuscle\Controller\AbstractSecuredActionController;

/**
 * Description of DashboardController
 *
 * @author: Awoyo Oluwatoyin Stephen alias talk2toyin / muscle53 <awoyotoyin@gmail.com>
 */
class DashboardController extends AbstractSecuredActionController
{
    public function indexAction()
    {
    	return new ViewModel(array());
    }
}