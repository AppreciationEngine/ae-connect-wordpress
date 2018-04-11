<?php

class AE_Notices {

    private $notice;
    private $notice_type;
    /**
     *
     * @param string $msg    notice message
     * @param string $notice_type error || updated || update-nag
     */
    public function set_notice($msg, $notice_type) {

        $this->notice_type = $notice_type;
        $this->notice = '<div class="' . $notice_type . ' notice is-dismissible">' .
        '<p>' . $msg . '</p>'.
        '</div>';

        echo $this->notice;

    }

}
