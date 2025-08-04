@php
    $currentRoute = Route::currentRouteName();
    $steps = [
        ['route' => 'welcome', 'icon' => 'bi-house', 'label' => 'step_welcome'],
        ['route' => 'installs', 'icon' => 'bi-asterisk', 'label' => 'step_requirements'],
        ['route' => 'database_import', 'icon' => 'bi-database', 'label' => 'step_database'],
        ['route' => 'profil_perusahaan', 'icon' => 'bi-building', 'label' => 'step_company'],
        ['route' => 'super_admin_config', 'icon' => 'bi-person', 'label' => 'step_admin'],
        ['route' => 'user_roles_list', 'icon' => 'bi-people', 'label' => 'step_roles'],
        ['route' => 'feature_toggles', 'icon' => 'bi-toggle-on', 'label' => 'step_features'],
        ['route' => 'finish', 'icon' => 'bi-check-circle', 'label' => 'step_finish']
    ];

    $currentStepIndex = collect($steps)->search(function ($step) use ($currentRoute) {
        return $step['route'] === $currentRoute;
    });

    function isStepActive($stepIndex, $currentStepIndex)
    {
        return $currentStepIndex !== false && $stepIndex <= $currentStepIndex;
    }
@endphp

<div class="container-fluid installer-content">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-10 col-md-10 col-lg-10 col-xl-9 text-center p-0 mt-3 mb-2">
            <div class="cardstep px-0 pt-4 pb-0 mt-3 mb-3">
                <h2 id="heading">{{ __('installer.install_title') }}</h2>
                <form id="msform" class="px-3">
                    <div class="d-flex justify-content-center align-items-center position-relative mb-5">
                        @foreach($steps as $index => $step)
                            <div class="col text-center position-relative">
                                <div class="circle-icon {{ isStepActive($index, $currentStepIndex) ? 'active' : '' }}">
                                    <i class="bi {{ $step['icon'] }}"></i>
                                </div>
                                <div class="step-label">{{ __('installer.' . $step['label']) }}</div>
                            </div>
                            @if($index < count($steps) - 1)
                                <hr class="connector-line {{ isStepActive($index + 1, $currentStepIndex) ? 'active' : '' }}" />
                            @endif
                        @endforeach
                    </div>
                    @php
                        $totalSteps = count($steps);
                        $currentStep = $currentStepIndex !== false ? $currentStepIndex + 1 : 1;
                        $progressPercentage = ($currentStep / $totalSteps) * 100;
                    @endphp
                    <div class="progress" role="progressbar" aria-label="Installation Progress"
                        aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-striped" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>