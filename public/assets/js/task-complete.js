document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la completion des tâches
    document.querySelectorAll('.complete-task-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            const taskItem = this.closest('.task-item');
            
            // Changer l'icône pendant le traitement
            this.style.pointerEvents = 'none';
            this.className = 'bi bi-arrow-repeat text-muted me-3 spin complete-task-btn';
            
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
                    this.style.pointerEvents = 'auto';
                    this.className = 'bi bi-check-circle text-muted me-3 complete-task-btn';
                    alert('Erreur lors de la completion de la tâche');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.style.pointerEvents = 'auto';
                this.className = 'bi bi-check-circle text-muted me-3 complete-task-btn';
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
            } else if (header.textContent.includes('Demain')) {
                header.innerHTML = '<i class="bi bi-calendar-plus me-2"></i>Demain (' + taskCount + ')';
            } else if (header.textContent.includes('Prochainement')) {
                header.innerHTML = '<i class="bi bi-calendar-range me-2"></i>Prochainement (' + taskCount + ')';
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
