document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM complètement chargé.');

    // Sélectionnez tous les achievements
    const achievements = document.querySelectorAll('.container-achievement');
    const profilePicture = document.querySelector('.img-profile-picture');

    if (!profilePicture) {
        console.error('Image de profil introuvable. Vérifiez la classe .img-profile-picture');
        return;
    }

    // Stockez la source originale de l'image de profil
    const originalSrc = profilePicture.src;

    // Ajoutez les événements de survol à chaque achievement
    achievements.forEach((achievement) => {
        achievement.addEventListener('mouseover', () => {
            const newSrc = achievement.getAttribute('data-image');
            if (newSrc) {
                profilePicture.src = newSrc;
            } else {
                console.error('L\'achievement ne contient pas d\'attribut data-image.');
            }
        });

        achievement.addEventListener('mouseout', () => {
            profilePicture.src = originalSrc;
        });
    });
});