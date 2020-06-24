<?php

namespace App\Nova\Tools;

use Gate;
use Laravel\Nova\Tool;

class DevTools extends Tool
{
    protected $links = [];

    public function boot()
    {
        $this->links['']['Horizon'] = '/horizon';
        $this->links['']['Monitoring'] = 'http://kube-prometheus-grafana.monitoring.svc.cluster.local/?orgId=1';
    }

    public function renderNavigation()
    {
        return Gate::check('viewNovaDevTools') ? view('admin.devtools', ['groupLinks' => $this->links]) : '';
    }
}
