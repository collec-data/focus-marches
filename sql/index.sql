--
-- Index pour la table `acheteur`
--
ALTER TABLE `acheteur`
    ADD UNIQUE KEY `id_acheteur` (`id_acheteur`);

--
-- Index pour la table `categories_juridiques`
--
ALTER TABLE `categories_juridiques`
    ADD PRIMARY KEY (`id_categories_juridiques`);

--
-- Index pour la table `cpv`
--
ALTER TABLE `cpv`
    ADD PRIMARY KEY (`id_cpv`);

--
-- Index pour la table `forme_prix`
--
ALTER TABLE `forme_prix`
    ADD PRIMARY KEY (`id_forme_prix`);

--
-- Index pour la table `lieu`
--
ALTER TABLE `lieu`
    ADD PRIMARY KEY (`id_lieu`);

--
-- Index pour la table `marche`
--
ALTER TABLE `marche`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `marche_titulaires`
--
ALTER TABLE `marche_titulaires`
    ADD PRIMARY KEY (`id_marche_titulaires`),
  ADD KEY `id_marche` (`id_marche`);

--
-- Index pour la table `mois`
--
ALTER TABLE `mois`
    ADD PRIMARY KEY (`id_mois`);

--
-- Index pour la table `naf`
--
ALTER TABLE `naf`
    ADD PRIMARY KEY (`id_naf`);

--
-- Index pour la table `nafa`
--
ALTER TABLE `nafa`
    ADD PRIMARY KEY (`id_nafa`);

--
-- Index pour la table `nature`
--
ALTER TABLE `nature`
    ADD PRIMARY KEY (`id_nature`);

--
-- Index pour la table `organismes`
--
ALTER TABLE `organismes`
    ADD PRIMARY KEY (`id_organisme`);

--
-- Index pour la table `procedure_marche`
--
ALTER TABLE `procedure_marche`
    ADD PRIMARY KEY (`id_procedure`);

--
-- Index pour la table `sirene`
--
ALTER TABLE `sirene`
    ADD PRIMARY KEY (`id_sirene`);

--
-- Index pour la table `titulaire`
--
ALTER TABLE `titulaire`
    ADD PRIMARY KEY (`id_titulaire`);

--
-- Index pour la table `tranches`
--
ALTER TABLE `tranches`
    ADD PRIMARY KEY (`id_tranche`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories_juridiques`
--
ALTER TABLE `categories_juridiques`
    MODIFY `id_categories_juridiques` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=307;

--
-- AUTO_INCREMENT pour la table `lieu`
--
ALTER TABLE `lieu`
    MODIFY `id_lieu` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `marche`
--
ALTER TABLE `marche`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `marche_titulaires`
--
ALTER TABLE `marche_titulaires`
    MODIFY `id_marche_titulaires` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `mois`
--
ALTER TABLE `mois`
    MODIFY `id_mois` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT pour la table `naf`
--
ALTER TABLE `naf`
    MODIFY `id_naf` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2110;

--
-- AUTO_INCREMENT pour la table `nafa`
--
ALTER TABLE `nafa`
    MODIFY `id_nafa` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;

--
-- AUTO_INCREMENT pour la table `organismes`
--
ALTER TABLE `organismes`
    MODIFY `id_organisme` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sirene`
--
ALTER TABLE `sirene`
    MODIFY `id_sirene` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tranches`
--
ALTER TABLE `tranches`
    MODIFY `id_tranche` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;
