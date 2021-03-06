<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\AdminTranslatableBundle\Controller;

use FSi\Bundle\AdminBundle\Controller\DisplayController as BaseDisplayController;
use FSi\Bundle\AdminBundle\Admin\Display\Element as DisplayElement;
use Symfony\Component\HttpFoundation\Request;

class TranslatableDisplayController extends BaseDisplayController
{
    public function displayAction(DisplayElement $element, Request $request)
    {
        return $this->handleRequest($element, $request, 'fsi_admin_translatable_display');
    }
}
