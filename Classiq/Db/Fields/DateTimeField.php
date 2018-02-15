<?php

namespace Classiq\Db\Fields;


class DateTimeField extends \DateTime {

    public function __toString(){
        return $this->format("Y-m-d H:i:s");
    }
}