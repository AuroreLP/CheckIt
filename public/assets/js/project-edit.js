/**
 * Gestion de l'édition de projet
 */
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editProjectBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const displayDiv = document.getElementById('projectDisplay');
    const editDiv = document.getElementById('projectEdit');
    
    // Vérifier que les éléments existent
    if (!editBtn || !cancelBtn || !displayDiv || !editDiv) {
        return; // Sortir si on n'est pas sur la page de projet
    }
    
    // Récupérer les valeurs originales depuis les attributs data
    const originalTitle = editBtn.dataset.originalTitle || '';
    const originalDomainId = editBtn.dataset.originalDomainId || '';
    const originalNeeds = editBtn.dataset.originalNeeds || '';
    
    // Passer en mode édition
    editBtn.addEventListener('click', function() {
        displayDiv.style.display = 'none';
        editDiv.style.display = 'block';
        editBtn.style.display = 'none';
        
        // Focus sur le premier champ
        const titleField = document.getElementById('projectTitle');
        if (titleField) {
            titleField.focus();
        }
    });
    
    // Annuler l'édition
    cancelBtn.addEventListener('click', function() {
        displayDiv.style.display = 'block';
        editDiv.style.display = 'none';
        editBtn.style.display = 'inline-block';
        
        // Remettre les valeurs originales
        const titleField = document.getElementById('projectTitle');
        const domainField = document.getElementById('projectDomain');
        const needsField = document.getElementById('projectNeeds');
        
        if (titleField) titleField.value = originalTitle;
        if (domainField) domainField.value = originalDomainId;
        if (needsField) needsField.value = originalNeeds;
    });
    
    // Confirmation avant de quitter si des modifications sont en cours
    let isEditing = false;
    
    editBtn.addEventListener('click', function() {
        isEditing = true;
    });
    
    cancelBtn.addEventListener('click', function() {
        isEditing = false;
    });
    
    // Avertir avant de quitter la page si des modifications sont en cours
    window.addEventListener('beforeunload', function(e) {
        if (isEditing && hasChanges()) {
            e.preventDefault();
            e.returnValue = 'Vous avez des modifications non sauvegardées. Voulez-vous vraiment quitter ?';
        }
    });
    
    // Vérifier s'il y a des changements
    function hasChanges() {
        const titleField = document.getElementById('projectTitle');
        const domainField = document.getElementById('projectDomain');
        const needsField = document.getElementById('projectNeeds');
        
        return (
            (titleField && titleField.value !== originalTitle) ||
            (domainField && domainField.value !== originalDomainId) ||
            (needsField && needsField.value !== originalNeeds)
        );
    }
});