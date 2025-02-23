document.addEventListener("DOMContentLoaded", function() {
    // Sélection des boutons toggle
    const cadreButton = document.getElementById("cadre");
    const nonCadreButton = document.getElementById("non-cadre");

    // Variable pour stocker la valeur sélectionnée
    let vcadre = '';

    // Écoute des clics sur les boutons toggle
    cadreButton.addEventListener("click", function() {
        vcadre = cadreButton.value;
        document.getElementById("cadreR").innerText = "Votre statut : Cadre";
    });

    nonCadreButton.addEventListener("click", function() {
        vcadre = nonCadreButton.value;
        document.getElementById("cadreR").innerText = "Votre statut : Non-cadre";
    });

    const calculateButton = document.querySelector('#calculateButton');
    if (calculateButton) {
        calculateButton.addEventListener('click', async () => {
            try {          
                const brutValue = parseFloat(document.querySelector('#brutInput').value);
                const nbMonthValue = parseFloat(document.querySelector('#nbMonth').value);
                var selectedOption = document.querySelector('input[name="option"]:checked');
                var optionValue = selectedOption ? selectedOption.value : ''; // Si aucun bouton n'est sélectionné, optionValue sera une chaîne vide

                if (!isNaN(brutValue) && !isNaN(nbMonthValue)) {
                    const brutMonthValue = brutValue / nbMonthValue;

                    document.querySelector('#truc').textContent = "Le salaire mensuel brut est estimé à : " + brutMonthValue + " €";

                    const request = await fetch("https://mon-entreprise.urssaf.fr/api/v1/evaluate", {
                        "headers": { "content-type": "application/json" },
                        "method": "POST",
                        "body": JSON.stringify({
                            "expressions": [
                                "salarié . rémunération . net . payé après impôt",
                                "salarié . contrat . salaire brut",
                                "salarié . rémunération . brut",
                                "salarié . cotisations . salarié"
                            ],
                            "situation": {
                                "entreprise . catégorie juridique": "''",
                                "entreprise . salariés . effectif . seuil": "'plus de 250'",
                                "salarié . contrat . statut cadre": vcadre,
                                "salarié . activité partielle": "non",
                                "salarié . rémunération . frais professionnels . trajets domicile travail . forfait mobilités durables . montant": 480,
                                "salarié . rémunération . frais professionnels . titres-restaurant . taux employeur": "60 %",
                                "salarié . rémunération . frais professionnels . titres-restaurant . montant unitaire": 9.05,
                                "salarié . rémunération . frais professionnels . titres-restaurant . nombre": "5 titres-restaurant/semaine * période . semaines par mois",
                                "salarié . rémunération . frais professionnels . titres-restaurant": "oui",
                                "salarié . contrat": "'CDI'",
                                "établissement . commune . département": "'Rhône'",
                                "impôt . méthode de calcul": "'taux neutre'",
                                "salarié . convention collective": "'droit commun'",
                                "salarié . contrat . salaire brut": brutMonthValue,
                            }
                        }),
                    });

                    // Vérifier si la requête est réussie
                    if (request.ok) {
                        // Parser la réponse JSON
                        const { evaluate } = await request.json();

                        // Afficher les données dans une table
                        console.table(evaluate);
                        const nodeValueIndex0 = parseFloat(evaluate[0].nodeValue).toFixed(2);
                        const resultDiv = document.getElementById("result");
                        resultDiv.textContent = "Le salaire net mensuel est estimé à : " + nodeValueIndex0 + " €";
                        
                        const nodeValueIndex3 = parseFloat(evaluate[3].nodeValue).toFixed(2);
                        const resultCSG = document.getElementById("resultCSG");
                        resultCSG.textContent = "La CSG est estimée à : " + nodeValueIndex3 + " €";
                        const containerA = document.querySelector('.containerA');
                        containerA.style.display = 'block';

                    } else {
                        throw new Error('Erreur lors de la récupération des données');
                    }
                } else {
                    document.querySelector('#truc').textContent = "Veuillez entrer des valeurs numériques.";
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des données:', error);
            }
        });
    } else {
        console.error('Aucun élément trouvé avec l\'identifiant "calculateButton"');
    }
});
