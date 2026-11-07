<?php

namespace Tests\Feature;

use App\Filament\Widgets\AccountWidget;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccountWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_account_widget_has_correct_properties(): void
    {
        $widget = new AccountWidget;

        // Test protected properties via reflection
        $reflection = new \ReflectionClass($widget);

        $maxWidthProp = $reflection->getProperty('maxWidth');
        $maxWidthProp->setAccessible(true);
        $this->assertEquals('full', $maxWidthProp->getValue($widget));

        $columnSpanProp = $reflection->getProperty('columnSpan');
        $columnSpanProp->setAccessible(true);
        $this->assertEquals('full', $columnSpanProp->getValue($widget));

        $viewProp = $reflection->getProperty('view');
        $viewProp->setAccessible(true);
        $this->assertEquals('filament.widgets.account-widget', $viewProp->getValue($widget));
    }

    public function test_shows_role_in_widget_view_data(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@greenhouse.test'],
            ['name' => 'Test User', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $user->assignRole($role);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Auth::guard('web')->setUser($user);

        // Instantiate directly instead of using ::make()
        $widget = new AccountWidget;

        // Call protected getViewData() via reflection
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getViewData');
        $method->setAccessible(true);
        $data = $method->invoke($widget);

        $this->assertEquals('admin@greenhouse.test', $data['user']->email);
        $this->assertEquals('super_admin', $data['roles']);
    }
}
