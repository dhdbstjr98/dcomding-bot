<?php
if(!defined("_ENGINE_INCLUDED_") || !_ENGINE_INCLUDED_) {
    exit;
}

echo format_rank_slack(Member::get_rank_with_name());