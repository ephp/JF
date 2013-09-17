<?php

namespace JF\ACLBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class JFACLBundle extends Bundle {

    public function getParent() {
        return 'EphpACLBundle';
    }

}
