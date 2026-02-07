document.addEventListener('DOMContentLoaded', function() {
    const employeeBody = document.getElementById('employeeBody');

    employeeBody.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-btn')) {
            const button = event.target;
            const row = button.closest('tr');
            const employeeId = button.getAttribute('data-id');
            const firstName = row.querySelector('td[data-field="first_name"]').textContent.trim();
            const lastName = row.querySelector('td[data-field="last_name"]').textContent.trim();

            if (confirm(`Are you sure you want to delete ${firstName} ${lastName}?`)) {
                fetch('PHP/management_table_delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: employeeId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        row.remove(); // Remove the row

                        // Create and show the message
                        const message = document.createElement('div');
                        message.className = 'success-message';
                        message.textContent = `Deleted employee: ${firstName} ${lastName} (ID: ${employeeId})`;
                        document.body.appendChild(message);

                        // Trigger fade in
                        setTimeout(() => {
                            message.classList.add('show');
                        }, 10); // Tiny delay to allow the CSS transition

                        // After 5 seconds, fade out and remove
                        setTimeout(() => {
                            message.classList.remove('show');
                            setTimeout(() => {
                                message.remove();
                            }, 500); // Wait for fade-out animation to finish before removing
                        }, 5000);
                    } else {
                        alert('Failed to delete employee: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting.');
                });
            }
        }
    });
});
