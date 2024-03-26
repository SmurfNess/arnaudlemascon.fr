from flask import Flask, request

app = Flask(__name__)

@app.route('/')
def index():
    # Demander le nom de l'utilisateur
    nom_utilisateur = request.args.get('nom', 'Inconnu')

    # Demander l'âge de l'utilisateur
    age_utilisateur = request.args.get('age', 0)

    # Convertir l'âge en entier
    age_utilisateur = int(age_utilisateur)

    # Vérifier si l'utilisateur est majeur ou mineur
    if age_utilisateur >= 18:
        message = "Bonjour {}, vous êtes majeur !".format(nom_utilisateur)
    else:
        message = "Bonjour {}, vous êtes mineur !".format(nom_utilisateur)

    return message

if __name__ == "__main__":
    app.run()
