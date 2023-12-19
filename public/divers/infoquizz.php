CREATE TABLE Make_user(
   id_user COUNTER,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50),
   email VARCHAR(50) NOT NULL,
   password VARCHAR(64) NOT NULL,
   role VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_user)
);

CREATE TABLE Category(
   id_category INT,
   libele VARCHAR(100) NOT NULL,
   PRIMARY KEY(id_category)
);

CREATE TABLE Formation(
   id_formation COUNTER,
   intitule VARCHAR(100) NOT NULL,
   PRIMARY KEY(id_formation)
);

CREATE TABLE Stagiaire(
   id_stagiaire INT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(200) NOT NULL,
   dateNaissance DATE NOT NULL,
   genre VARCHAR(1) NOT NULL,
   ville VARCHAR(50) NOT NULL,
   email VARCHAR(50) NOT NULL,
   tel VARCHAR(10) NOT NULL,
   PRIMARY KEY(id_stagiaire)
);

CREATE TABLE Session(
   id_session INT,
   nbPlaceTotal INT NOT NULL,
   dateDebut DATETIME NOT NULL,
   dateFin DATETIME NOT NULL,
   intitule VARCHAR(100) NOT NULL,
   id_user INT NOT NULL,
   id_formation INT NOT NULL,
   PRIMARY KEY(id_session),
   FOREIGN KEY(id_user) REFERENCES Make_user(id_user),
   FOREIGN KEY(id_formation) REFERENCES Formation(id_formation)
);

CREATE TABLE Modules(
   id_modules COUNTER,
   libele VARCHAR(250) NOT NULL,
   id_category INT NOT NULL,
   PRIMARY KEY(id_modules),
   FOREIGN KEY(id_category) REFERENCES Category(id_category)
);

CREATE TABLE Programme(
   id_session INT,
   id_modules INT,
   nbJours INT NOT NULL,
   PRIMARY KEY(id_session, id_modules),
   FOREIGN KEY(id_session) REFERENCES Session(id_session),
   FOREIGN KEY(id_modules) REFERENCES Modules(id_modules)
);

CREATE TABLE Formation_stagiaire(
   id_session INT,
   id_stagiaire INT,
   PRIMARY KEY(id_session, id_stagiaire),
   FOREIGN KEY(id_session) REFERENCES Session(id_session),
   FOREIGN KEY(id_stagiaire) REFERENCES Stagiaire(id_stagiaire)
);
