<?php

namespace Ens\JobeetBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EnsJobeetBundle extends Bundle
{
	public function getParent()
	{
		return 'SonataAdminBundle';
	}
}
