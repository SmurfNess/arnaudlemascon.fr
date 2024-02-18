document.addEventListener('DOMContentLoaded', () => {
    const calculateButton = document.querySelector('#calculateButton');
    if (calculateButton) {
        calculateButton.addEventListener('click', async () => {
            try {
                const brutValue = parseFloat(document.querySelector('#brutInput').value);
                const nbMonthValue = parseFloat(document.querySelector('#nbMonth').value);

                if (!isNaN(brutValue) && !isNaN(nbMonthValue)) {
                    const brutMonthValue = brutValue / nbMonthValue;

                    document.querySelector('#truc').textContent = "Le résultat de l'addition est : " + brutMonthValue;

                    const request = await fetch("https://mon-entreprise.urssaf.fr/api/v1/evaluate", {
                        "headers": { "content-type": "application/json" },
                        "method": "POST",
                        "body": JSON.stringify({
                            "expressions": [
                                "salarié . rémunération . net . payé après impôt",
                                "salarié . contrat . salaire brut",
                                "salarié . rémunération . brut"
                            ],
                            "situation": {
                                "entreprise . catégorie juridique": "''",
                                "entreprise . salariés . effectif . seuil": "'plus de 250'",
                                "salarié . contrat . statut cadre": "oui",
                                "salarié . rémunération . frais professionnels . trajets domicile travail . forfait mobilités durables . montant": 480,
                                "salarié . rémunération . frais professionnels . titres-restaurant . taux employeur": "60 %",
                                "salarié . rémunération . frais professionnels . titres-restaurant . montant unitaire": 9.05,
                                "salarié . rémunération . frais professionnels . titres-restaurant . nombre": "5 titres-restaurant/semaine * période . semaines par mois",
                                "salarié . rémunération . frais professionnels . titres-restaurant": "oui",
                                "salarié . contrat": "'CDI'",
                                "établissement . commune . département": "'Rhône'",
                                "impôt . méthode de calcul": "'taux neutre'",
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
                        const nodeValueIndex0 = evaluate[0].nodeValue;
                        const resultDiv = document.getElementById("result");
                        resultDiv.textContent = "nodeValue pour l'index 0 : " + nodeValueIndex0;   
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
