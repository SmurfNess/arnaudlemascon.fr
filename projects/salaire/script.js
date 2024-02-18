document.addEventListener('DOMContentLoaded', () => {
    const calculateButton = document.querySelector('#calculateButton');
    if (calculateButton) {
        calculateButton.addEventListener('click', async () => {
            try {
                // Récupérer la valeur de l'input brutInput
                const brutValue = document.querySelector('#brutInput').value;

                // Afficher la valeur dans la div avec l'ID "truc"
                document.querySelector('#truc').innerHTML = brutValue;

                // Fetch data from the URSSAF API
                const request = await fetch("https://mon-entreprise.urssaf.fr/api/v1/evaluate", {
                    "headers": { "content-type": "application/json" },
                    "method": "POST",
                    "body": JSON.stringify({
                        "expressions": [
                            "salarié . rémunération . net . payé après impôt",
                            "salarié . contrat . statut cadre",
                            "salarié . cotisations . exonérations . JEI"
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
                            "salarié . contrat . salaire brut": brutValue,
                        }
                    }),
                });

                // Parse the JSON response
                const { evaluate } = await request.json();

                // Display the data in a table
                console.table(evaluate);
                const nodeValueIndex0 = evaluate[0].nodeValue;
                const resultDiv = document.getElementById("result");
                resultDiv.textContent = "nodeValue pour l'index 0 : " + nodeValueIndex0;   

            } catch (error) {
                // Handle errors
                console.error('Error fetching data:', error);
            }
        });
    } else {
        console.error('No element found with id "calculateButton"');
    }
});
