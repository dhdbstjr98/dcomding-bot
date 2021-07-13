<?php
if(!defined("_ENGINE_INCLUDED_") || !_ENGINE_INCLUDED_) {
    exit;
}

echo format_weekstatus_slack(Member::get_weekstatus_with_name());