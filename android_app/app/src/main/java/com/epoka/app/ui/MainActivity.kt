package com.epoka.app.ui

import android.content.Intent
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import com.epoka.app.SessionManager
import com.epoka.app.databinding.ActivityMainBinding

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var session: SessionManager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        session = SessionManager(this)

        if (!session.isLoggedIn()) {
            startActivity(Intent(this, LoginActivity::class.java))
            finish()
            return
        }

        setSupportActionBar(binding.toolbar)
        supportActionBar?.title = "Missions Epoka"

        // Salutation : "Bonjour [Prénom] [Fonction]"
        binding.tvGreeting.text = "Bonjour ${session.getPrenom()} ${session.getFonction()}"

        binding.btnAddMission.setOnClickListener {
            startActivity(Intent(this, CreateMissionActivity::class.java))
        }

        binding.btnQuit.setOnClickListener {
            finishAffinity()
        }
    }
}
