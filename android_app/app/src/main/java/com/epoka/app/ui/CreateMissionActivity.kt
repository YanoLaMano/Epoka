package com.epoka.app.ui

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.MenuItem
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.epoka.app.SessionManager
import com.epoka.app.adapter.VilleAdapter
import com.epoka.app.api.RetrofitClient
import com.epoka.app.databinding.ActivityCreateMissionBinding
import com.epoka.app.model.CreateMissionRequest
import com.epoka.app.model.Ville
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class CreateMissionActivity : AppCompatActivity() {

    private lateinit var binding: ActivityCreateMissionBinding
    private lateinit var session: SessionManager

    private var villes: List<Ville> = emptyList()
    private var dateDebut: Calendar? = null
    private var dateFin:   Calendar? = null

    private val sdf        = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
    private val sdfDisplay = SimpleDateFormat("dd/MM/yyyy", Locale.FRENCH)

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCreateMissionBinding.inflate(layoutInflater)
        setContentView(binding.root)

        session = SessionManager(this)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        supportActionBar?.title = "Nouvelle mission"

        binding.etDateDebut.setOnClickListener { pickDate(isDebut = true) }
        binding.etDateFin.setOnClickListener   { pickDate(isDebut = false) }

        binding.btnEnvoyer.setOnClickListener { submit() }

        loadVilles()
    }

    override fun onOptionsItemSelected(item: MenuItem): Boolean {
        if (item.itemId == android.R.id.home) { finish(); return true }
        return super.onOptionsItemSelected(item)
    }

    private fun pickDate(isDebut: Boolean) {
        val cal = Calendar.getInstance()
        DatePickerDialog(this, { _, y, m, d ->
            val picked = Calendar.getInstance().apply { set(y, m, d) }
            if (isDebut) {
                dateDebut = picked
                binding.etDateDebut.setText(sdfDisplay.format(picked.time))
            } else {
                dateFin = picked
                binding.etDateFin.setText(sdfDisplay.format(picked.time))
            }
        }, cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH)).show()
    }

    private fun loadVilles() {
        lifecycleScope.launch {
            try {
                val response = RetrofitClient.api.getVilles()
                if (response.isSuccessful) {
                    villes = response.body() ?: emptyList()
                    // Remplissage du Spinner — pattern code/libellé (cours)
                    val adapter = VilleAdapter(this@CreateMissionActivity, villes)
                    binding.spinnerVille.adapter = adapter
                }
            } catch (_: Exception) {
                showError("Impossible de charger les villes")
            }
        }
    }

    private fun submit() {
        // Validation
        if (dateDebut == null) { showError("Veuillez saisir la date de début"); return }
        if (dateFin   == null) { showError("Veuillez saisir la date de fin");   return }
        if (dateDebut!!.after(dateFin)) {
            showError("La date de fin doit être après la date de début"); return
        }
        if (villes.isEmpty()) { showError("Les villes ne sont pas chargées"); return }

        // Récupération du code (id) de la ville sélectionnée — pattern code/libellé
        val villeArrivee = binding.spinnerVille.selectedItem as? Ville
            ?: run { showError("Sélectionnez une ville de destination"); return }

        // La ville de départ = ville de l'agence du salarié connecté
        val idVilleDepart = session.getIdVilleAgence()
        if (idVilleDepart == -1) {
            showError("Agence du salarié introuvable"); return
        }

        // Intitulé auto-généré
        val intitule = "Mission du ${sdfDisplay.format(dateDebut!!.time)}"

        binding.btnEnvoyer.isEnabled = false
        binding.tvError.visibility   = View.GONE

        val request = CreateMissionRequest(
            intitule       = intitule,
            dateDebut      = sdf.format(dateDebut!!.time),
            dateFin        = sdf.format(dateFin!!.time),
            idVilleDepart  = idVilleDepart,
            idVilleArrivee = villeArrivee.id,
            idSalarie      = session.getSalarieId()
        )

        lifecycleScope.launch {
            try {
                val response = RetrofitClient.api.createMission(request)
                if (response.isSuccessful && response.body()?.success == true) {
                    // Afficher le message de confirmation dans le même écran
                    binding.tvConfirmation.visibility = View.VISIBLE
                    binding.btnEnvoyer.isEnabled      = false
                } else {
                    showError(response.body()?.error ?: "Erreur lors de la création")
                    binding.btnEnvoyer.isEnabled = true
                }
            } catch (_: Exception) {
                showError("Impossible de joindre le serveur")
                binding.btnEnvoyer.isEnabled = true
            }
        }
    }

    private fun showError(msg: String) {
        binding.tvError.text       = msg
        binding.tvError.visibility = View.VISIBLE
    }
}
