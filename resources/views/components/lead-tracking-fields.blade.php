<input type="hidden" name="consent_version" value="2026-09-v1">
<input type="hidden" name="utm_source" data-lead-tracking="utm_source">
<input type="hidden" name="utm_medium" data-lead-tracking="utm_medium">
<input type="hidden" name="utm_campaign" data-lead-tracking="utm_campaign">
<input type="hidden" name="utm_content" data-lead-tracking="utm_content">
<input type="hidden" name="utm_term" data-lead-tracking="utm_term">
<input type="hidden" name="landing_page" data-lead-tracking="landing_page">
<input type="hidden" name="referrer" data-lead-tracking="referrer">

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    const keys = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term'
    ];

    const params = new URLSearchParams(window.location.search);

    keys.forEach(function (key) {
        const storageKey = 'worksitem_first_' + key;
        const queryValue = params.get(key);

        if (queryValue && !sessionStorage.getItem(storageKey)) {
            sessionStorage.setItem(storageKey, queryValue.substring(0, 150));
        }

        const value = sessionStorage.getItem(storageKey) || queryValue || '';

        document.querySelectorAll(
            '[data-lead-tracking="' + key + '"]'
        ).forEach(function (input) {
            input.value = value.substring(0, 150);
        });
    });

    document.querySelectorAll(
        '[data-lead-tracking="landing_page"]'
    ).forEach(function (input) {
        input.value = window.location.href.substring(0, 2000);
    });

    document.querySelectorAll(
        '[data-lead-tracking="referrer"]'
    ).forEach(function (input) {
        input.value = document.referrer.substring(0, 2000);
    });
});
</script>
@endonce