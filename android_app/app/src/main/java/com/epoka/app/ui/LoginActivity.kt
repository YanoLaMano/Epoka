package com.epoka.app.ui

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.epoka.app.R
import com.epoka.app.SessionManager
import com.epoka.app.api.RetrofitClient
import com.epoka.app.databinding.ActivityLoginBinding
import com.epoka.app.model.LoginRequest
import kotlinx.coroutines.launch
import java.util.Calendar

class LoginActivity : AppCompatActivity() {

    private lateinit var binding: ActivityLoginBinding
    private lateinit var session: SessionManager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLoginBinding.inflate(layoutInflater)
        setContentView(binding.root)

        session = SessionManager(this)

        // Si déjà connecté → aller directement à MainActivity
        if (session.isLoggedIn()) {
            goToMain()
            return
        }

        // Salutation dynamique selon l'heure
        val hour = Calendar.getInstance().get(Calendar.HOUR_OF_DAY)
        binding.tvGreeting.text = when {
            hour in 5..11  -> "Bonjour !"
            hour in 12..17 -> "Bon après-midi !"
            else           -> "Bonsoir !"
        }

        binding.btnLogin.setOnClickListener { doLogin() }
    }

    private fun doLogin() {
        val idStr    = binding.etId.text.toString().trim()
        val password = binding.etPassword.text.toString()

        if (idStr.isEmpty()) {
            binding.tilId.error = "Identifiant requis"
            return
        }
        if (password.isEmpty()) {
            binding.tilPassword.error = "Mot de passe requis"
            return
        }

        binding.tilId.error       = null
        binding.tilPassword.error = null
        setLoading(true)

        lifecycleScope.launch {
            try {
                val response = RetrofitClient.api.login(LoginRequest(idStr.toInt(), password))
                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.success == true && body.salarie != null) {
                        session.saveSession(body.salarie)
                        goToMain()
                    } else {
                        showError(body?.error ?: "Identifiant ou mot de passe incorrect")
                    }
                } else {
                    showError("Identifiant ou mot de passe incorrect")
                }
            } catch (e: Exception) {
                showError("Impossible de joindre le serveur.\nVérifiez votre connexion.")
            } finally {
                setLoading(false)
            }
        }
    }

    private fun setLoading(loading: Boolean) {
        binding.btnLogin.isEnabled = !loading
        binding.btnLogin.text      = if (loading) "Connexion…" else "Se connecter"
        binding.progressBar.visibility = if (loading) View.VISIBLE else View.GONE
    }

    private fun showError(msg: String) {
        binding.tvError.text       = msg
        binding.cardError.visibility = View.VISIBLE
    }

    private fun goToMain() {
        startActivity(Intent(this, MainActivity::class.java))
        finish()
    }
}
