window.onload = () => {
    //charger des bouttons "Supprimer"
    let links = document.querySelectorAll("[data-delete]");
    //consol.log(links);

    //on boucle sur links

    for (link of links) {
        //on ecoute le clic
        link.addEventListener("click", function(e) {
            //on empéche la navigation
            e.preventDefault();
            //on demande confirmation
            if (confirm("Voulez vous supprimer cette image?")) {
                //on envoi une requete Ajax vers le href du lien avec la méthode delete
                fetch(this.getAttribute("href"), {
                    method: 'DELETE',
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ "_token": this.dataset.token })
                }).then(
                    //on récupère la reponce en JSON
                    response => response.json()
                ).then(data => {
                    if (data.success) {
                        //on supprime le div qui contien l'image
                        this.parentElement.remove()
                    } else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}