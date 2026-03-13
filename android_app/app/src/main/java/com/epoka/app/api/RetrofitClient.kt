package com.epoka.app.api

import com.epoka.app.BuildConfig
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object RetrofitClient {

    /**
     * URL de base de l'API.
     * - Émulateur Android  → http://10.0.2.2/Epoka/api/
     * - Vrai appareil      → http://<IP_DE_VOTRE_PC>/Epoka/api/
     *   (ex: http://192.168.1.42/Epoka/api/)
     * Définie dans app/build.gradle via buildConfigField.
     */
    private val BASE_URL: String = BuildConfig.BASE_URL

    private val client = OkHttpClient.Builder()
        .connectTimeout(10, TimeUnit.SECONDS)
        .readTimeout(15, TimeUnit.SECONDS)
        .addInterceptor(HttpLoggingInterceptor().apply {
            level = HttpLoggingInterceptor.Level.BODY
        })
        .build()

    val api: ApiService by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }
}
