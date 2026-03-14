package com.epoka.app.model

import com.google.gson.annotations.SerializedName

data class Salarie(
    val id: Int,
    val nom: String,
    val prenom: String,
    val fonction: String,
    @SerializedName("peut_valider")    val peutValider: Int,
    @SerializedName("peut_payer")      val peutPayer: Int,
    @SerializedName("id_agence")       val idAgence: Int?,
    @SerializedName("id_ville_agence") val idVilleAgence: Int?
)

data class Ville(
    val id: Int,
    val nom: String,
    @SerializedName("code_postal") val codePostal: String
) {
    override fun toString() = "$nom ($codePostal)"
}

data class Mission(
    val id: Int,
    val intitule: String,
    @SerializedName("date_debut")       val dateDebut: String,
    @SerializedName("date_fin")         val dateFin: String,
    @SerializedName("id_ville_depart")  val idVilleDepart: Int,
    @SerializedName("id_ville_arrivee") val idVilleArrivee: Int,
    @SerializedName("id_salarie")       val idSalarie: Int,
    val statut: String,
    @SerializedName("nb_repas")         val nbRepas: Int = 0,

    // Champs joints (retournés par l'API)
    @SerializedName("ville_depart_nom")  val villeDepartNom: String? = null,
    @SerializedName("cp_depart")         val cpDepart: String? = null,
    @SerializedName("ville_arrivee_nom") val villeArriveeNom: String? = null,
    @SerializedName("cp_arrivee")        val cpArrivee: String? = null,
    @SerializedName("salarie_nom")       val salarieNom: String? = null,
    @SerializedName("salarie_prenom")    val salariePrenom: String? = null
) {
    val statutLabel: String get() = when (statut) {
        "brouillon" -> "Brouillon"
        "validee"   -> "Validée"
        "payee"     -> "Payée"
        else        -> statut
    }
    val trajetLabel: String get() =
        "${villeDepartNom ?: "?"} → ${villeArriveeNom ?: "?"}"
}

// Requête de création
data class CreateMissionRequest(
    val intitule: String,
    @SerializedName("date_debut")       val dateDebut: String,
    @SerializedName("date_fin")         val dateFin: String,
    @SerializedName("id_ville_depart")  val idVilleDepart: Int,
    @SerializedName("id_ville_arrivee") val idVilleArrivee: Int,
    @SerializedName("id_salarie")       val idSalarie: Int,
    @SerializedName("nb_repas")         val nbRepas: Int = 0
)

// Réponses API
data class LoginRequest(val id: Int, @SerializedName("mot_de_passe") val motDePasse: String)
data class LoginResponse(val success: Boolean, val salarie: Salarie?, val error: String?)
data class CreateResponse(val success: Boolean, val id: Int?, val error: String?)
data class DeleteResponse(val success: Boolean, val deleted: Int?, val error: String?)
