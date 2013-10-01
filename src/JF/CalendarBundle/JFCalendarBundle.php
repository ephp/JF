<?php

namespace JF\CalendarBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class JFCalendarBundle extends Bundle {

    public function getParent() {
        return 'EphpCalendarBundle';
    }

}
