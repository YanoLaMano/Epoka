package com.epoka.app.ui

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.MenuItem
import android.view.View
import android.widget.ArrayAdapter
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.epoka.app.SessionManager
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
    private val sdf = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
    private val sdfDisplay = SimpleDateFormat("dd/MM/yyyy", Locale.FRENCH)

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCreateMissionBinding.inflate(layoutInflater)
        setContentView(binding.root)

        session = SessionManager(this)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
        supportActionBar?.title = "Nouvelle mission"

        setupDatePickers()
        loadVilles()

        binding.btnCreate.setOnClickListener { submit() }
    }

    override fun onOptionsItemSelected(item: MenuItem): Boolean {
        if (item.itemId == android.R.id.home) { finish(); return true }
        return super.onOptionsItemSelected(item)
    }

    private fun setupDatePickers() {
        binding.etDateDebut.setOnClickListener { pickDate(isDebut = true) }
        binding.etDateFin.setOnClickListener   { pickDate(isDebut = false) }
        // Pas de clavier sur ces champs
        binding.etDateDebut.isFocusable = false
        binding.etDateFin.isFocusable   = false
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
                    val villeNames = villes.map { it.toString() }
                    val adapterVille = ArrayAdapter(
                        this@CreateMissionActivity,
                        android.R.layout.simple_dropdown_item_1line,
                        villeNames
                    )
                    binding.actvDepart.setAdapter(adapterVille)
                    binding.actvArrivee.setAdapter(adapterVille)
                    binding.actvDepart.threshold = 1
                    binding.actvArrivee.threshold = 1
                }
            } catch (_: Exception) {
                showError("Impossible de charger les villes")
            }
        }
    }

    private fun getSelectedVille(text: String): Ville? =
        villes.firstOrNull { it.toString() == text }

    private fun submit() {
        val intitule = binding.etIntitule.text.toString().trim()
        val departText  = binding.actvDepart.text.toString()
        val arriveeText = binding.actvArrivee.text.toString()
        val nbRepas = binding.etNbRepas.text.toString().toIntOrNull() ?: 0

        // Validation
        if (intitule.isEmpty()) { binding.tilIntitule.error = "Intitulé requis"; return }
        binding.tilIntitule.error = null

        if (dateDebut == null) { binding.tilDateDebut.error = "Date de début requise"; return }
        binding.tilDateDebut.error = null

        if (dateFin == null) { binding.tilDateFin.error = "Date de fin requise"; return }
        binding.tilDateFin.error = null

        if (dateDebut!!.after(dateFin)) {
            binding.tilDateFin.error = "La date de fin doit être après la date de début"
            return
        }
        binding.tilDateFin.error = null

        val villeDepart  = getSelectedVille(departText)
        val villeArrivee = getSelectedVille(arriveeText)

        if (villeDepart == null)  { binding.tilDepart.error  = "Ville de départ invalide";  return }
        if (villeArrivee == null) { binding.tilArrivee.error = "Ville d'arrivée invalide"; return }
        binding.tilDepart.error  = null
        binding.tilArrivee.error = null

        if (villeDepart.id == villeArrivee.id) {
            binding.tilArrivee.error = "La ville d'arrivée doit être différente du départ"
            return
        }

        setLoading(true)

        val request = CreateMissionRequest(
            intitule      = intitule,
            dateDebut     = sdf.format(dateDebut!!.time),
            dateFin       = sdf.format(dateFin!!.time),
            idVilleDepart = villeDepart.id,
            idVilleArrivee= villeArrivee.id,
            idSalarie     = session.getSalarieId(),
            nbRepas       = nbRepas
        )

        lifecycleScope.launch {
            try {
                val response = RetrofitClient.api.createMission(request)
                if (response.isSuccessful && response.body()?.success == true) {
                    finish() // Retour à la liste
                } else {
                    showError(response.body()?.error ?: "Erreur lors de la création")
                }
            } catch (e: Exception) {
                showError("Impossible de joindre le serveur")
            } finally {
                setLoading(false)
            }
        }
    }

    private fun setLoading(loading: Boolean) {
        binding.btnCreate.isEnabled = !loading
        binding.btnCreate.text = if (loading) "Création…" else "Créer la mission"
        binding.progressBar.visibility = if (loading) View.VISIBLE else View.GONE
    }

    private fun showError(msg: String) {
        binding.tvError.text = msg
        binding.cardError.visibility = View.VISIBLE
    }
}
