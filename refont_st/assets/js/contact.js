    // Sélectionner les éléments
    const messageInput = document.getElementById('message-input');
    const conditionalRow = document.getElementById('conditional-row');

    // Ajouter un écouteur d'événement
    messageInput.addEventListener('input', function () {
        if (messageInput.value.trim().length > 0) {
            conditionalRow.style.display = 'flex'; // Affiche la rangée
        } else {
            conditionalRow.style.display = 'none'; // Cache la rangée
        }
    });
