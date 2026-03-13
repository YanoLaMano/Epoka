package com.epoka.app.adapter

import android.content.Context
import android.widget.ArrayAdapter
import android.widget.Filter
import com.epoka.app.model.Ville

/**
 * Adapter liste déroulante code/libellé pour les villes.
 * Affiche : "Nom de la ville (code_postal)"
 * Stocke  : l'objet Ville complet (id = code, nom+cp = libellé)
 */
class VilleAdapter(context: Context, private val villes: List<Ville>) :
    ArrayAdapter<Ville>(context, android.R.layout.simple_dropdown_item_1line, villes) {

    private var filtered: List<Ville> = villes

    override fun getCount() = filtered.size
    override fun getItem(position: Int) = filtered[position]

    override fun getFilter(): Filter = object : Filter() {
        override fun performFiltering(constraint: CharSequence?): FilterResults {
            val results = FilterResults()
            val query = constraint?.toString()?.lowercase()?.trim() ?: ""
            results.values = if (query.isEmpty()) villes
            else villes.filter {
                it.nom.lowercase().contains(query) || it.codePostal.contains(query)
            }
            @Suppress("UNCHECKED_CAST")
            results.count = (results.values as List<Ville>).size
            return results
        }

        override fun publishResults(constraint: CharSequence?, results: FilterResults?) {
            @Suppress("UNCHECKED_CAST")
            filtered = results?.values as? List<Ville> ?: emptyList()
            notifyDataSetChanged()
        }
    }
}
