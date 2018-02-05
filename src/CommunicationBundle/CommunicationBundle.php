<?php

namespace CommunicationBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class CommunicationBundle extends AbstractPimcoreBundle
{
	use PackageVersionTrait;

	protected function getComposerPackageName(): string
	{
		return 'docono/communication-bundle';
	}
}
