package com.epoka.app.adapter

import android.content.Context
import android.widget.ArrayAdapter
import com.epoka.app.model.Ville

/**
 * Adapter liste déroulante code/libellé pour les villes (Spinner).
 * Affiche : "Nom de la ville (code_postal)"  → toString() de Ville
 * Retourne : l'objet Ville complet (id = code)
 * Pattern identique au complément Java du cours.
 */
class VilleAdapter(context: Context, villes: List<Ville>) :
    ArrayAdapter<Ville>(context, android.R.layout.simple_spinner_item, villes) {

    init {
        setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
    }
}
