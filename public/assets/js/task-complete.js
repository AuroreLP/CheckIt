document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la completion des tâches
    document.querySelectorAll('.complete-task-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            const taskItem = this.closest('.task-item');
            
            // Désactiver le bouton pendant le traitement
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-arrow-repeat spin"></i>';
            
            // Envoyer la requête AJAX
            fetch('complete-task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `task_id=${taskId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Faire disparaître la tâche avec animation
                    taskItem.style.transition = 'opacity 0.3s ease-out';
                    taskItem.style.opacity = '0';
                    
                    setTimeout(() => {
                        taskItem.remove();
                        
                        // Mettre à jour les compteurs
                        updateTaskCounters();
                    }, 300);
                } else {
                    // Restaurer le bouton en cas d'erreur
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-check"></i>';
                    alert('Erreur lors de la completion de la tâche');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-check"></i>';
                alert('Erreur de connexion');
            });
        });
    });
    
    function updateTaskCounters() {
        // Mettre à jour les compteurs dans les en-têtes
        document.querySelectorAll('.card-header h5').forEach(header => {
            const cardBody = header.closest('.card').querySelector('.card-body');
            const taskCount = cardBody.querySelectorAll('.task-item').length;
            
            if (header.textContent.includes('Aujourd\'hui')) {
                header.innerHTML = '<i class="bi bi-calendar-day me-2"></i>Aujourd\'hui (' + taskCount + ')';
            } else if (header.textContent.includes('en retard')) {
                header.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Tâches en retard (' + taskCount + ')';
            }
        });
    }
});

// Animation CSS pour le spinner
document.head.insertAdjacentHTML('beforeend', `
<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
`);
