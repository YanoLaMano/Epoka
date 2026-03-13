package com.epoka.app.ui

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.epoka.app.SessionManager
import com.epoka.app.adapter.MissionAdapter
import com.epoka.app.api.RetrofitClient
import com.epoka.app.databinding.ActivityMainBinding
import com.epoka.app.model.Mission
import kotlinx.coroutines.launch

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var session: SessionManager
    private lateinit var adapter: MissionAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        session = SessionManager(this)

        // Vérifier session
        if (!session.isLoggedIn()) { logout(); return }

        setupToolbar()
        setupRecyclerView()

        binding.fabCreate.setOnClickListener {
            startActivity(Intent(this, CreateMissionActivity::class.java))
        }
    }

    override fun onResume() {
        super.onResume()
        loadMissions()
    }

    private fun setupToolbar() {
        setSupportActionBar(binding.toolbar)
        binding.tvUserName.text = session.getFullName()
        binding.tvUserFonction.text = session.getFonction()
        binding.btnLogout.setOnClickListener { confirmLogout() }
    }

    private fun setupRecyclerView() {
        adapter = MissionAdapter { mission -> confirmDelete(mission) }
        binding.recyclerView.layoutManager = LinearLayoutManager(this)
        binding.recyclerView.adapter = adapter
    }

    private fun loadMissions() {
        setLoading(true)
        lifecycleScope.launch {
            try {
                val response = RetrofitClient.api.getMissions(session.getSalarieId())
                if (response.isSuccessful) {
                    val missions = response.body() ?: emptyList()
                    adapter.submitList(missions)
                    binding.tvEmpty.visibility = if (missions.isEmpty()) View.VISIBLE else View.GONE
                    binding.recyclerView.visibility = if (missions.isEmpty()) View.GONE else View.VISIBLE
                } else {
                    showNetworkError()
                }
            } catch (e: Exception) {
                showNetworkError()
            } finally {
                setLoading(false)
            }
        }
    }

    private fun confirmDelete(mission: Mission) {
        AlertDialog.Builder(this)
            .setTitle("Supprimer la mission")
            .setMessage("Supprimer « ${mission.intitule} » ?")
            .setPositiveButton("Supprimer") { _, _ -> deleteMission(mission) }
            .setNegativeButton("Annuler", null)
            .show()
    }

    private fun deleteMission(mission: Mission) {
        lifecycleScope.launch {
            try {
                val response = RetrofitClient.api.deleteMission(mission.id)
                if (response.isSuccessful) loadMissions()
            } catch (_: Exception) {}
        }
    }

    private fun confirmLogout() {
        AlertDialog.Builder(this)
            .setTitle("Déconnexion")
            .setMessage("Voulez-vous vous déconnecter ?")
            .setPositiveButton("Oui") { _, _ -> logout() }
            .setNegativeButton("Non", null)
            .show()
    }

    private fun logout() {
        session.clearSession()
        startActivity(Intent(this, LoginActivity::class.java))
        finish()
    }

    private fun setLoading(loading: Boolean) {
        binding.progressBar.visibility = if (loading) View.VISIBLE else View.GONE
        binding.recyclerView.visibility = if (loading) View.GONE else View.VISIBLE
    }

    private fun showNetworkError() {
        binding.tvEmpty.text = "Erreur de connexion au serveur"
        binding.tvEmpty.visibility = View.VISIBLE
    }
}
