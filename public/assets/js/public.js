document.addEventListener('DOMContentLoaded', function() {
    const roleSelector = document.getElementById('role');
    const passcodeField = document.getElementById('passcodeField');
    const passcode_input = document.getElementById('passcode');

    if (roleSelector && passcodeField) {
        roleSelector.addEventListener('change', function() {
            const selectedRole = this.value;
            if (selectedRole === 'president' || selectedRole === 'super_admin') {
                passcodeField.style.display = 'block';
                passcode_input.setAttribute('required', 'required');

            } else {
                passcodeField.style.display = 'none';
                passcode_input.removeAttribute('required');
            }
        });
    }
});
