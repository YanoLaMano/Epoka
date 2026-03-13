package com.epoka.app.api

import com.epoka.app.model.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    @POST("auth.php")
    suspend fun login(@Body request: LoginRequest): Response<LoginResponse>

    @GET("villes.php")
    suspend fun getVilles(): Response<List<Ville>>

    @GET("missions.php")
    suspend fun getMissions(@Query("id_salarie") idSalarie: Int): Response<List<Mission>>

    @POST("missions.php")
    suspend fun createMission(@Body request: CreateMissionRequest): Response<CreateResponse>

    @DELETE("missions.php")
    suspend fun deleteMission(@Query("id") id: Int): Response<DeleteResponse>
}
