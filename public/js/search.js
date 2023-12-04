////////////////////////////////////// search bar dynamique (AJAX) ///////////////////////////////////////

const textHint = document.getElementById("textHint");

const linkResult = document.createElement("A");

const valueResult = document.createElement("P");

const categoryResult = document.createElement("h4");
const levelResult = document.createElement("h5");
linkResult.classList.add("search_link_item");

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
           
        );
        console.log("response = ", response);

        const data = await response.json();

        console.log("data = ", data);
        if(data) {

            const resultsByCategoryAndDifficulty = {};

            data.forEach((info) => {
                const category = info["category_label"];
                const difficulty = info["level_label"];
        
                if (!resultsByCategoryAndDifficulty[category]) {
                    resultsByCategoryAndDifficulty[category] = {};
                }
        
                if (!resultsByCategoryAndDifficulty[category][difficulty]) {
                    resultsByCategoryAndDifficulty[category][difficulty] = [];
                }
        
                resultsByCategoryAndDifficulty[category][difficulty].push(info);
            });
        
            // Afficher les résultats par catégorie et difficulté
            for (const category in resultsByCategoryAndDifficulty) {
                const difficulties = resultsByCategoryAndDifficulty[category];
        
                const categoryHeader = document.createElement("h5");
                categoryHeader.textContent = category;
                textHint.appendChild(categoryHeader);
        
                for (const difficulty in difficulties) {
                    const results = difficulties[difficulty];
        
                    const difficultyHeader = document.createElement("ol");
                    difficultyHeader.textContent = difficulty + ':';
                    textHint.appendChild(difficultyHeader);
        
                    results.forEach((info) => {
                        const resultLink = document.createElement("a");
                        resultLink.href = `/quiz/play/${info.id}`; // Lien vers le quiz
                        resultLink.textContent = info.title; // Titre du quiz
        
                        const resultItem = document.createElement("li");
                        resultItem.appendChild(resultLink);
                        difficultyHeader.appendChild(resultItem);
                    });
                }
            }
        
    

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