<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\CompanyBundle;

use HeimrichHannot\CompanyBundle\DependencyInjection\ContaoHeimrichHannotCompanyExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoCompanyBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new ContaoHeimrichHannotCompanyExtension();
    }
}
