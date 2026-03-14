package com.epoka.app

import android.content.Context
import com.epoka.app.model.Salarie

/**
 * Stocke la session de l'utilisateur connecté dans SharedPreferences.
 */
class SessionManager(context: Context) {

    private val prefs = context.getSharedPreferences("epoka_session", Context.MODE_PRIVATE)

    fun saveSession(salarie: Salarie) {
        prefs.edit().apply {
            putInt("id",              salarie.id)
            putString("nom",          salarie.nom)
            putString("prenom",       salarie.prenom)
            putString("fonction",     salarie.fonction)
            putBoolean("peut_valider",    salarie.peutValider == 1)
            putBoolean("peut_payer",      salarie.peutPayer   == 1)
            putInt("id_ville_agence", salarie.idVilleAgence ?: -1)
            apply()
        }
    }

    fun isLoggedIn(): Boolean = prefs.getInt("id", -1) != -1

    fun getSalarieId(): Int  = prefs.getInt("id", -1)
    fun getNom(): String     = prefs.getString("nom", "") ?: ""
    fun getPrenom(): String  = prefs.getString("prenom", "") ?: ""
    fun getFonction(): String= prefs.getString("fonction", "") ?: ""
    fun peutValider(): Boolean = prefs.getBoolean("peut_valider", false)
    fun peutPayer(): Boolean   = prefs.getBoolean("peut_payer", false)
    fun getFullName(): String       = "${getPrenom()} ${getNom()}"
    fun getIdVilleAgence(): Int     = prefs.getInt("id_ville_agence", -1)

    fun clearSession() = prefs.edit().clear().apply()
}
