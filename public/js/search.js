////////////////////////////////////// search bar dynamique (AJAX) ///////////////////////////////////////

// la function est appelé avec comme parametre le contenu de la search bar
// function showHintOld(srch) {
//     if (srch.length == 0) {
//         // si la search bar est vide, le menu n'est pas affiché 
//         textHint.innerHTML = "";    
//         textHint.classList.remove("dropDownMenuHint");
        
//     } else {
//         // si la search bar a du contenu, une requête XML est faite et stocké comme object dans une variable
//         const xmlhttp = new XMLHttpRequest();

//         // quand la requête à terminé de charger, textHint(la div ou le resultat de la recherche est affiché) aura comme contenu le resultat de la requête
//         xmlhttp.onload = function() {
//             textHint.innerHTML = this.responseText;
//         }
        
//         // une requête GET est crée dans le xml, elle renvoie vers une action "search" et le contenu de la search bar
//         xmlhttp.open("GET", "index.php?action=search&srch=" + srch);

//         // envoie la requête qui sera intercepté par l'index
//         xmlhttp.send();

//         // et le menu sera affiché 
//         textHint.classList.add("dropDownMenuHint");
//     }
// }
const textHint = document.getElementById("textHint");

const linkResult = document.createElement("A");

const valueResult = document.createElement("P");

const categoryResult = document.createElement("span");


// la function est appelé avec comme parametre le contenu de la search bar
async function showHint(srch) {
    if (srch.length == 0) {
        // si la search bar est vide, le menu n'est pas affiché 
        textHint.innerHTML = "";    
        textHint.classList.remove("dropDownMenuHint");
        
    } else {
        while(textHint.childNodes.length > 0) {
            textHint.removeChild(child[0]);
        }  
        // appel asynchrone
        const response = await fetch(
            "/search/bar/" + srch
            // {
            //     method: "GET",
            //     headers: {},
            //     body: {},
            // }
        );
        console.log("response = ", response);

        const data = await response.json();
        // const data = await JSON.parse(response);

        console.log("data = ", data);
        if(data) {

            data.forEach((info) => {
                console.log('info',info);
                const newValueResult = valueResult.cloneNode();
                const newLinkResult = linkResult.cloneNode();
                const newCategoryResult = categoryResult.cloneNode();

                newLinkResult.href = info["link"];
                newValueResult.textContent = info["label"];
                newCategoryResult.textContent = info["category"]+" -> ";

                textHint.appendChild(newLinkResult);
                newLinkResult.appendChild(newValueResult);
                newValueResult.prepend(newCategoryResult);
            });

        } else {
            
        }

        textHint.classList.add("dropDownMenuHint");
    }
}

// XMLHttpRequest : bas niveau, permet de mettre en place la solution sans aucune contrainte, peu importe le matériel, nécessite uniquement JS
// fetch : librairie incluse dans tous les navigateurs, fonctionnera très bien dans la partie front d'une app, nécessitera d'être importée/installée dans un back
// $.ajax (JQuery) : nécessite JQuery, très semblable à fetch, fait automatiquement la transformation de JSON en objet exploitable
// axios : librairie à importer/installer obligatoirement, met à disposition beaucoup de fonctionnalités autour des requêtes/réponses HTTP

// div qui contient le resultat de la recherche
// const textHint = document.getElementById("textHint");

// id de la search bar
const searchBar = document.getElementById("searchInput");

// la function est appelé à chaque fois qu'une touche est 'up' après avoir appuyé dessus
searchBar.addEventListener("keyup", () => {
    showHint(searchBar.value)
});

// recupère toutes les balises enfants de textHint 
const child = textHint.childNodes;

// ferme le dropdown menu de la bar de recherche et la vide quand on clique dans la page 
window.addEventListener("click", function() {
    if(textHint.classList.contains("dropDownMenuHint")) {
        while(child.length > 0) {
            textHint.removeChild(child[0]);
        }      
        textHint.classList.remove("dropDownMenuHint");
    }
});