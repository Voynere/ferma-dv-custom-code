<?php


namespace WCSTORES\WC\Exception;


class Exception extends \Exception {

    /**
     * Sanitized error code.
     *
     * @var string
     */
    protected $sErrorCode;

    /**
     * Error extra data.
     *
     * @var array
     */
    protected $aErrorData;


    public function __construct( $sCode, $sMessage, $sHttpStatusCode = 400, $aData = array() ) {
        $this->sErrorCode = $sCode;
        $this->aErrorData = array_merge( array( 'status' => $sHttpStatusCode ), $aData );

        parent::__construct( $sMessage, $sHttpStatusCode );
    }

    /**
     * Returns the error code.
     *
     * @return string
     */
    public function getErrorCode() {
        return $this->sErrorCode;
    }

    /**
     * Returns error data.
     *
     * @return array
     */
    public function getErrorData() {
        return $this->aErrorData;
    }
}