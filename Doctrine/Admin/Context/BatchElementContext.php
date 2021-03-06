<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\AdminTranslatableBundle\Doctrine\Admin\Context;

use FSi\Bundle\AdminBundle\Admin\CRUD\Context\BatchElementContext as BaseBatchElementContext;
use FSi\Bundle\AdminTranslatableBundle\Manager\LocaleManager;
use Symfony\Component\Form\FormBuilderInterface;

class BatchElementContext extends BaseBatchElementContext
{
    /**
     * @var LocaleManager
     */
    private $localeManager;

    public function __construct($requestHandlers, FormBuilderInterface $formBuilder, LocaleManager $localeManager)
    {
        parent::__construct($requestHandlers, $formBuilder);
        $this->localeManager = $localeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = parent::getData();
        $data['translatable_locale'] = $this->localeManager->getLocale();

        return $data;
    }
}
