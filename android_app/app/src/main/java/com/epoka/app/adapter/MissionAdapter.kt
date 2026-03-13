package com.epoka.app.adapter

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.epoka.app.R
import com.epoka.app.databinding.ItemMissionBinding
import com.epoka.app.model.Mission

class MissionAdapter(
    private val onDelete: (Mission) -> Unit
) : ListAdapter<Mission, MissionAdapter.ViewHolder>(DiffCb()) {

    inner class ViewHolder(private val b: ItemMissionBinding) :
        RecyclerView.ViewHolder(b.root) {

        fun bind(m: Mission) {
            b.tvIntitule.text  = m.intitule
            b.tvTrajet.text    = m.trajetLabel
            b.tvDates.text     = "${m.dateDebut}  →  ${m.dateFin}"
            b.tvStatut.text    = m.statutLabel

            // Couleur du badge statut
            val (bgColor, textColor) = when (m.statut) {
                "validee" -> Pair(R.color.statut_validee_bg, R.color.statut_validee_text)
                "payee"   -> Pair(R.color.statut_payee_bg,   R.color.statut_payee_text)
                else      -> Pair(R.color.statut_brouillon_bg, R.color.statut_brouillon_text)
            }
            b.tvStatut.setBackgroundColor(ContextCompat.getColor(b.root.context, bgColor))
            b.tvStatut.setTextColor(ContextCompat.getColor(b.root.context, textColor))

            // Supprimer uniquement si brouillon
            b.btnDelete.visibility = if (m.statut == "brouillon")
                android.view.View.VISIBLE else android.view.View.GONE
            b.btnDelete.setOnClickListener { onDelete(m) }
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) =
        ViewHolder(ItemMissionBinding.inflate(LayoutInflater.from(parent.context), parent, false))

    override fun onBindViewHolder(holder: ViewHolder, position: Int) =
        holder.bind(getItem(position))

    class DiffCb : DiffUtil.ItemCallback<Mission>() {
        override fun areItemsTheSame(a: Mission, b: Mission) = a.id == b.id
        override fun areContentsTheSame(a: Mission, b: Mission) = a == b
    }
}
