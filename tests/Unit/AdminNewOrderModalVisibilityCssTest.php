<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * يمنع انحدار إشعار الطلب الجديد في لوحة الأدمن: الصوت يعمل بينما الـ modal يبقى مخفيًا
 * عندما يكون JS هو Bootstrap 3 (.in) والثيم يتوقع Bootstrap 5 (.show).
 */
class AdminNewOrderModalVisibilityCssTest extends TestCase
{
    private function adminCustomCssPath(): string
    {
        return public_path('assets/admin/css/custom.css');
    }

    /** @test */
    public function custom_css_defines_bs3_modal_in_visibility_bridge(): void
    {
        $path = $this->adminCustomCssPath();
        $this->assertFileExists($path, 'Admin custom.css must exist for modal visibility bridge.');

        $css = file_get_contents($path);
        $this->assertNotFalse($css);

        $this->assertMatchesRegularExpression(
            '/\.modal\.in\s*,\s*\n\.modal\.show\s*\{[^}]*display\s*:\s*block/s',
            $css,
            'Expected .modal.in,.modal.show { display: block ... } for BS3 .in and BS5 .show.'
        );

        $this->assertMatchesRegularExpression(
            '/\.modal\.fade\.in\s+\.modal-dialog\s*,\s*\n\.modal\.fade\.show\s+\.modal-dialog\s*\{[^}]*transform\s*:\s*none/s',
            $css,
            'Expected .modal.fade.in/.fade.show .modal-dialog { transform: none } for visible dialog.'
        );

        $this->assertMatchesRegularExpression(
            '/\.modal-backdrop\.fade\.in\s*,\s*\n\.modal-backdrop\.fade\.show\s*\{[^}]*opacity\s*:\s*1/s',
            $css,
            'Expected .modal-backdrop.fade.in/.fade.show { opacity: 1 } for visible backdrop.'
        );

        $this->assertMatchesRegularExpression(
            '/body\.modal-open\s+\.modal\.in\s*,[\s\S]*?z-index\s*:\s*1000010\s*!important/s',
            $css,
            'Expected modal z-index above toast container (999999) and backdrop so dialog is visible.'
        );

        $this->assertStringContainsString('body.modal-open .modal.show', $css);
        $this->assertMatchesRegularExpression(
            '/body\.modal-open\s+\.modal\.fade\.show\s*\{[^}]*z-index\s*:\s*1000010/s',
            $css,
            'Expected BS5 .modal.show/.modal.fade.show block to set z-index 1000010 above backdrop (1000000).'
        );
    }

    /** @test */
    public function admin_layout_includes_popup_modal_markup_and_script(): void
    {
        $layout = resource_path('views/layouts/admin/app.blade.php');
        $this->assertFileExists($layout);

        $blade = file_get_contents($layout);
        $this->assertNotFalse($blade);

        $this->assertStringContainsString('id="popup-modal"', $blade);
        $this->assertStringContainsString("$('#popup-modal')", $blade);
        $this->assertStringContainsString(".modal('show')", $blade);
        $this->assertStringContainsString('class="btn btn-primary check-contact"', $blade);
        $this->assertStringContainsString('data-admin-notify-url', $blade);
        $this->assertStringContainsString('window.adminGoAfterNotifyClick', $blade);
        $this->assertStringContainsString('window.adminStripNotifyModalsFromDom', $blade);
        $this->assertStringContainsString('window.adminPrepareNotifyModalForShow', $blade);
        $this->assertStringContainsString('removeProperty(\'display\')', $blade);
        $this->assertStringContainsString('elite_admin_snooze_contact_us', $blade);
        $this->assertStringContainsString('lastAdminStoreData', $blade);
        $this->assertStringContainsString('addEventListener(\'click\'', $blade);
    }
}
