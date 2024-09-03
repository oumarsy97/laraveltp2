<?php
/**
 * @OA\Schema(
 *     schema="Dette",
 *     type="object",
 *     title="Dette",
 *     description="Model representing a Dette",
 *     required={"id", "montantTotal", "montantRestant", "dateEcheance"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID de la dette"
 *     ),
 *     @OA\Property(
 *         property="montantTotal",
 *         type="number",
 *         format="float",
 *         description="Montant total de la dette"
 *     ),
 *     @OA\Property(
 *         property="montantRestant",
 *         type="number",
 *         format="float",
 *         description="Montant restant de la dette"
 *     ),
 *     @OA\Property(
 *         property="dateEcheance",
 *         type="string",
 *         format="date",
 *         description="Date d'échéance de la dette"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date de création de la dette"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date de mise à jour de la dette"
 *     )
 * )
 */
/**
 * @OA\Schema(
 *     schema="StoreDetteRequest",
 *     type="object",
 *     title="StoreDetteRequest",
 *     required={"clientId", "montant", "articles", "paiement"},
 *     @OA\Property(
 *         property="clientId",
 *         type="integer",
 *         description="ID du client"
 *     ),
 *     @OA\Property(
 *         property="montant",
 *         type="number",
 *         format="float",
 *         description="Montant total de la dette"
 *     ),
 *     @OA\Property(
 *         property="articles",
 *         type="array",
 *         description="Liste des articles associés à la dette",
 *         @OA\Items(
 *             @OA\Property(
 *                 property="articleId",
 *                 type="integer",
 *                 description="ID de l'article"
 *             ),
 *             @OA\Property(
 *                 property="qteVente",
 *                 type="integer",
 *                 description="Quantité vendue"
 *             ),
 *             @OA\Property(
 *                 property="prixVente",
 *                 type="number",
 *                 format="float",
 *                 description="Prix de vente de l'article"
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="paiement",
 *         type="object",
 *         description="Informations sur le paiement",
 *         @OA\Property(
 *             property="montant",
 *             type="number",
 *             format="float",
 *             description="Montant du paiement"
 *         )
 *     )
 * )
 */

 /**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article",
 *     description="Schéma représentant un article.",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Identifiant unique de l'article",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="libelle",
 *         type="string",
 *         description="Libelle de l'article",
 *         example="T-shirt"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description de l'article",
 *         example="T-shirt en coton, taille M"
 *     ),
 *     @OA\Property(
 *         property="prix",
 *         type="number",
 *         format="float",
 *         description="Prix de l'article",
 *         example=19.99
 *     ),
 *     @OA\Property(
 *         property="qteStock",
 *         type="integer",
 *         description="Quantité en stock de l'article",
 *         example=100
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date de création de l'article",
 *         example="2024-09-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date de dernière mise à jour de l'article",
 *         example="2024-09-01T12:00:00Z"
 *     )
 * )
 */


 /**
 * @OA\Schema(
 *     schema="StoreArticleRequest",
 *     type="object",
 *     title="StoreArticleRequest",
 *     description="Schéma représentant la requête pour créer un nouvel article.",
 *     required={"nom", "description", "prix", "qteStock"},
 *     @OA\Property(
 *         property="nom",
 *         type="string",
 *         description="Nom de l'article",
 *         example="T-shirt"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description de l'article",
 *         example="T-shirt en coton, taille M"
 *     ),
 *     @OA\Property(
 *         property="prix",
 *         type="number",
 *         format="float",
 *         description="Prix de l'article",
 *         example=19.99
 *     ),
 *     @OA\Property(
 *         property="qteStock",
 *         type="integer",
 *         description="Quantité en stock de l'article",
 *         example=100
 *     ),
 *     @OA\Property(
 *         property="photo",
 *         type="string",
 *         format="binary",
 *         description="Image de l'article (optionnel)",
 *         example="photo.jpg"
 *     )
 * )
 */
/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model schema",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier for the user"
 *     ),
 *     @OA\Property(
 *         property="prenom",
 *         type="string",
 *         description="First name of the user"
 *     ),
 *     @OA\Property(
 *         property="nom",
 *         type="string",
 *         description="Last name of the user"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email address of the user"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="Password for the user"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Phone number of the user"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         description="Role of the user in the system"
 *     ),
 *     example={
 *         "id": 1,
 *         "prenom": "John",
 *         "nom": "Doe",
 *         "email": "john.doe@example.com",
 *         "password": "password123",
 *         "phone": "+1234567890",
 *         "role": "admin"
 *     }
 * )
 */
