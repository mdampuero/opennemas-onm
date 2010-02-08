      
    <div class="CContainerLogoConectaYPestanyasPortadaPC">
        <a href="/conecta/" border=0> <div class="CContainerLogoPortadaConecta"></div></a>
        <div class="CContainerPestanyasConectaPC">
            <!-- PESTANYA -->
            <div class="pestanyaSelecList">
                <div class="contInfoPestanyaGrande">
                    <div class="flechaPestanyaNoSelecList"></div>
                    <a href="/conecta/"> <div class="textoPestanyaNoSelecListPC">CONECT@</div></a>
                </div>
                <div class="cierrePestanyaNoSelecList"></div>
            </div>
           
{*
            <div class="pestanyaSelecList">
                <div class="contInfoPestanyaGrande">
                    <div class="flechaPestanyaSelecListPC"></div>
                    <a href="/conecta/envio">  <div class="textoPestanyaSelecListPC">PARTICIPA</div></a>
                </div>
                <div class="cierrePestanyaSelecList"></div>
            </div>

         *}

        </div>
    </div>
    <!-- **************************** ENVIAR NOTICIA ******************************** -->
    <form name="envio" id="envio" method="post" action="#" enctype="multipart/form-data"> 	  
        <div class="CZonaEnvioNoticia">
            <div class="textoConectaXornal">Enviar informaci&oacute;n</div>
            {if $message}
                <div id="message" style="float:right;border:1px;background-color:#AAA;padding:10px;"><b>{$message}</b></div>
            {/if}
            <div class="CZonaDarseDeAlta">
                <div class="CPunto1EnvioNoticia">
                 
                    <div class="CCabeceraFormulario1">
                        <span class="CDestacadoEnvioNoticia">
                            1. Selecciona y/o adjunta el tipo de archivo que quieres publicar
                        </span>
                    </div>
                    <div class="CZonaRadioButtons">
                        <div class="CContenedorRadioButton">
                            <label><input type="radio" name="tipoArchivo" value="1" checked="checked" />Fotografía</label>
                        </div>

                        <div class="CContenedorRadioButton">
                            <label><input type="radio" name="tipoArchivo" value="2" />Vídeo</label>
                        </div>

                        <div class="CContenedorRadioButton">
                            <label><input type="radio" name="tipoArchivo" value="4" />Opini&oacute;n</label>
                        </div>
                           <div class="CContenedorRadioButton">
                            <label><input type="radio" name="tipoArchivo" value="3" />Carta al Director</label>
                        </div>
                    </div>

                     <div class="CFragmentoFormulario">
                        <div class="CTemasPrincipalesEnvio">
                            <div class="CLabelSelecTemas">Temas principales: </div>
                            <div class="CComboSelecTemas">
                                <select name="temasPrincipales" id="temasPrincipales">
                                    {* Mirar Javascript
                                     *}
                                </select>
                            </div>
                        </div>

                    </div>
				<div id="div_AdjuntarArchivos" style="display:inline;">
		                    <div class="CZonaAdjuntarArchivos"  >
		                     {*   <div class="CBotonExaminar">
		                            <a href="#"><img alt="Examinar" src="{$params.IMAGE_DIR}envio_noticia/botonExaminar.gif"/></a>
		                        </div> *}
		                        <div class="CInputNombreArchivo">
                                    <!-- MAX_FILE_SIZE must precede the file input field - 20MB -->
                                    <input type="hidden" name="MAX_FILE_SIZE" value="20971520" />
                                    <input type="file" name="file" />
                                </div>
		                   {*     <div class="CAdjuntarMas"> <img src="{$params.IMAGE_DIR}noticia/flecha_destacado.gif" alt="imagen"/><a href="#">Adjuntar más archivos</a></div>*}
		                     </div>						  
		                    <div class="CInfoAttach">
		                        <b>Tipo de archivo:</b> El archivo ha de ser de tipo JPG, GIF o PNG, cualquier otro archivo no es válido. <br />
                                <b>Tamaño de archivo:</b> Las imágenes no pueden superar los 300KB. Los vídeos no deben exceder los 20Mb.		                   	
		                    </div>
                    </div>
                    
                    <div id="div_codigoVideo" style="display:none;">
                     <div class="CInputNombreArchivo"> <br> C&oacute;digo: <input type="text" name="codigoVideo" />
				<select name="temasPrincipales">
                                    <option value="1"> Youtube </option>                                   
                                </select>
                                <div class="CInfoAttach" style="position: relative;">
                                    <b>Codigo:</b> url o código que proporciona el sitio web que aloja el video.
                                    Por ejemplo, youtube: FuQexBk40zM
                                
                                    <div id="youtubeSearchControl"></div>
		               			</div>
		               </div>
					</div>
                </div>

                <div class="fileteHorizontalEnvioNota"></div>

                <div class="CPunto2EnvioNoticia">
                    <div class="CCabeceraFormulario1">
                        <span class="CDestacadoEnvioNoticia">
                            2. Describe tu envío
                        </span>
                    </div>
                    <div class="CZonaDescrEnvio">
                        <div class="CTituloDescrEnvio">
                            <div class="CLabelTituloDescrEnvio"><label for="tituloArticulo">Título: <sup>*</sup></label></div>
                            <div class="CInputTituloDescrEnvio">
                                <input type="text" class="required" maxlength="2000"  size="52" name="tituloArticulo" id="tituloArticulo" value="" onBlur="javascript:get_tags(this.value);"/>
                            </div>
                        </div>
                        <div class="CContenidoDescrEnvio">
                            <div class="CLabelContenidoDescrEnvio">Contenido del artículo:</div>
                            <div class="CInputContenidoDescrEnvio">
                                <textarea name="textoArticulo" id='textoArticulo' cols="60" rows="5" onkeypress="textCounter(this,this.form.counter,2000);"></textarea>
                                
                            </div>
                        </div>
                        <div class="CTextoDescrEnvio">
                            <b>Tamaño máximo:</b> <input type="text"  class="required" name="counter" maxlength="4" value="2000" size="4" onblur="textCounter(this.form.counter,this,2000);"> No puede exceder de 2.000 carácteres
                        </div>
                                
                        <div class="CTituloDescrEnvio">
                            <div class="CLabelTituloDescrEnvio">Palabras Clave: </div>
                            <div class="CInputTituloDescrEnvio">
                                 <input type="text" class="required" size="52" name="palabrasClave" id="palabrasClave" value="" />
                            </div>
                            <div class="CTextoDescrEnvio">Escribe las palabras separadas por comas</div>

                        </div>
                     </div>
                </div>

               {* <div class="fileteHorizontalEnvioNota"></div>

                <div class="CPunto3EnvioNoticia">
                    <div class="CCabeceraFormulario1">
                        <span class="CDestacadoEnvioNoticia">
                            3. Categoría del envío
                        </span>
                    </div>
                    <div class="CFragmentoFormulario">
                        <div class="CTemasPrincipalesEnvio">
                            <div class="CLabelSelecTemas">Temas principales: </div>
                            <div class="CComboSelecTemas">
                                <select name="temasPrincipales" id="temasPrincipales">
                                      Mirar Javascript
                                     
                                </select>
                            </div>
                        </div>
                       
                    </div>

                </div>
                *}
                <div class="fileteHorizontalEnvioNota"></div>

                <div class="CPunto4EnvioNoticia">
                    <div class="CCabeceraFormulario1">
                        <span class="CDestacadoEnvioNoticia">
                            4. Lugar y fecha de la información
                        </span>
                    </div>

                    <div class="CFragmentoFormulario">
                        <div class="CZonaPaisLugarYFecha">
                            <div class="CLabelPaisLugarYFecha">País:</div>
                            <div class="CComboPaisLugarYFecha">
                                <select name="selectPais">
                                  <option value="AF">Afganistán</option>
                                  <option value="AL">Albania</option>
                                  <option value="DE">Alemania</option>
                                  <option value="AD">Andorra</option>
                                  <option value="AO">Angola</option>
                                  <option value="AI">Anguilla</option>
                                  <option value="AQ">Antártida</option>
                                  <option value="AG">Antigua y Barbuda</option>
                                  <option value="AN">Antillas Holandesas</option>
                                  <option value="SA">Arabia Saudí</option>
                                  <option value="DZ">Argelia</option>
                                  <option value="AR">Argentina</option>
                                  <option value="AM">Armenia</option>
                                  <option value="AW">Aruba</option>
                                  <option value="AU">Australia</option>
                                  <option value="AT">Austria</option>
                                  <option value="AZ">Azerbaiyán</option>
                                  <option value="BS">Bahamas</option>
                                  <option value="BH">Bahrein</option>
                                  <option value="BD">Bangladesh</option>
                                  <option value="BB">Barbados</option>
                                  <option value="BE">Bélgica</option>
                                  <option value="BZ">Belice</option>
                                  <option value="BJ">Benin</option>
                                  <option value="BM">Bermudas</option>
                                  <option value="BY">Bielorrusia</option>
                                  <option value="MM">Birmania</option>
                                  <option value="BO">Bolivia</option>
                                  <option value="BA">Bosnia y Herzegovina</option>
                                  <option value="BW">Botswana</option>
                                  <option value="BR">Brasil</option>
                                  <option value="BN">Brunei</option>
                                  <option value="BG">Bulgaria</option>
                                  <option value="BF">Burkina Faso</option>
                                  <option value="BI">Burundi</option>
                                  <option value="BT">Bután</option>
                                  <option value="CV">Cabo Verde</option>
                                  <option value="KH">Camboya</option>
                                  <option value="CM">Camerún</option>
                                  <option value="CA">Canadá</option>
                                  <option value="TD">Chad</option>
                                  <option value="CL">Chile</option>
                                  <option value="CN">China</option>
                                  <option value="CY">Chipre</option>
                                  <option value="VA">Ciudad del Vaticano (Santa Sede)</option>
                                  <option value="CO">Colombia</option>
                                  <option value="KM">Comores</option>
                                  <option value="CG">Congo</option>
                                  <option value="CD">Congo, República Democrática del</option>
                                  <option value="KR">Corea</option>
                                  <option value="KP">Corea del Norte</option>
                                  <option value="CI">Costa de Marfíl</option>
                                  <option value="CR">Costa Rica</option>
                                  <option value="HR">Croacia (Hrvatska)</option>
                                  <option value="CU">Cuba</option>
                                  <option value="DK">Dinamarca</option>
                                  <option value="DJ">Djibouti</option>
                                  <option value="DM">Dominica</option>
                                  <option value="EC">Ecuador</option>
                                  <option value="EG">Egipto</option>
                                  <option value="SV">El Salvador</option>
                                  <option value="AE">Emiratos Árabes Unidos</option>
                                  <option value="ER">Eritrea</option>
                                  <option value="SI">Eslovenia</option>
                                  <option selected="selected" value="ES">España</option>
                                  <option value="US">Estados Unidos</option>
                                  <option value="EE">Estonia</option>
                                  <option value="ET">Etiopía</option>
                                  <option value="FJ">Fiji</option>
                                  <option value="PH">Filipinas</option>
                                  <option value="FI">Finlandia</option>
                                  <option value="FR">Francia</option>
                                  <option value="GA">Gabón</option>
                                  <option value="GM">Gambia</option>
                                  <option value="GE">Georgia</option>
                                  <option value="GH">Ghana</option>
                                  <option value="GI">Gibraltar</option>
                                  <option value="GD">Granada</option>
                                  <option value="GR">Grecia</option>
                                  <option value="GL">Groenlandia</option>
                                  <option value="GP">Guadalupe</option>
                                  <option value="GU">Guam</option>
                                  <option value="GT">Guatemala</option>
                                  <option value="GY">Guayana</option>
                                  <option value="GF">Guayana Francesa</option>
                                  <option value="GN">Guinea</option>
                                  <option value="GQ">Guinea Ecuatorial</option>
                                  <option value="GW">Guinea-Bissau</option>
                                  <option value="HT">Haití</option>
                                  <option value="HN">Honduras</option>
                                  <option value="HU">Hungría</option>
                                  <option value="IN">India</option>
                                  <option value="ID">Indonesia</option>
                                  <option value="IQ">Irak</option>
                                  <option value="IR">Irán</option>
                                  <option value="IE">Irlanda</option>
                                  <option value="BV">Isla Bouvet</option>
                                  <option value="CX">Isla de Christmas</option>
                                  <option value="IS">Islandia</option>
                                  <option value="KY">Islas Caimán</option>
                                  <option value="CK">Islas Cook</option>
                                  <option value="CC">Islas de Cocos o Keeling</option>
                                  <option value="FO">Islas Faroe</option>
                                  <option value="HM">Islas Heard y McDonald</option>
                                  <option value="FK">Islas Malvinas</option>
                                  <option value="MP">Islas Marianas del Norte</option>
                                  <option value="MH">Islas Marshall</option>
                                  <option value="UM">Islas menores de Estados Unidos</option>
                                  <option value="PW">Islas Palau</option>
                                  <option value="SB">Islas Salomón</option>
                                  <option value="SJ">Islas Svalbard y Jan Mayen</option>
                                  <option value="TK">Islas Tokelau</option>
                                  <option value="TC">Islas Turks y Caicos</option>
                                  <option value="VI">Islas Vírgenes (EE.UU.)</option>
                                  <option value="VG">Islas Vírgenes (Reino Unido)</option>
                                  <option value="WF">Islas Wallis y Futuna</option>
                                  <option value="IL">Israel</option>
                                  <option value="IT">Italia</option>
                                  <option value="JM">Jamaica</option>
                                  <option value="JP">Japón</option>
                                  <option value="JO">Jordania</option>
                                  <option value="KZ">Kazajistán</option>
                                  <option value="KE">Kenia</option>
                                  <option value="KG">Kirguizistán</option>
                                  <option value="KI">Kiribati</option>
                                  <option value="KW">Kuwait</option>
                                  <option value="LA">Laos</option>
                                  <option value="LS">Lesotho</option>
                                  <option value="LV">Letonia</option>
                                  <option value="LB">Líbano</option>
                                  <option value="LR">Liberia</option>
                                  <option value="LY">Libia</option>
                                  <option value="LI">Liechtenstein</option>
                                  <option value="LT">Lituania</option>
                                  <option value="LU">Luxemburgo</option>
                                  <option value="MK">Macedonia, Ex-República Yugoslava de</option>
                                  <option value="MG">Madagascar</option>
                                  <option value="MY">Malasia</option>
                                  <option value="MW">Malawi</option>
                                  <option value="MV">Maldivas</option>
                                  <option value="ML">Malí</option>
                                  <option value="MT">Malta</option>
                                  <option value="MA">Marruecos</option>
                                  <option value="MQ">Martinica</option>
                                  <option value="MU">Mauricio</option>
                                  <option value="MR">Mauritania</option>
                                  <option value="YT">Mayotte</option>
                                  <option value="MX">México</option>
                                  <option value="FM">Micronesia</option>
                                  <option value="MD">Moldavia</option>
                                  <option value="MC">Mónaco</option>
                                  <option value="MN">Mongolia</option>
                                  <option value="MS">Montserrat</option>
                                  <option value="MZ">Mozambique</option>
                                  <option value="NA">Namibia</option>
                                  <option value="NR">Nauru</option>
                                  <option value="NP">Nepal</option>
                                  <option value="NI">Nicaragua</option>
                                  <option value="NE">Níger</option>
                                  <option value="NG">Nigeria</option>
                                  <option value="NU">Niue</option>
                                  <option value="NF">Norfolk</option>
                                  <option value="NO">Noruega</option>
                                  <option value="NC">Nueva Caledonia</option>
                                  <option value="NZ">Nueva Zelanda</option>
                                  <option value="OM">Omán</option>
                                  <option value="NL">Países Bajos</option>
                                  <option value="PA">Panamá</option>
                                  <option value="PG">Papúa Nueva Guinea</option>
                                  <option value="PK">Paquistán</option>
                                  <option value="PY">Paraguay</option>
                                  <option value="PE">Perú</option>
                                  <option value="PN">Pitcairn</option>
                                  <option value="PF">Polinesia Francesa</option>
                                  <option value="PL">Polonia</option>
                                  <option value="PT">Portugal</option>
                                  <option value="PR">Puerto Rico</option>
                                  <option value="QA">Qatar</option>
                                  <option value="UK">Reino Unido</option>
                                  <option value="CF">República Centroafricana</option>
                                  <option value="CZ">República Checa</option>
                                  <option value="ZA">República de Sudáfrica</option>
                                  <option value="DO">República Dominicana</option>
                                  <option value="SK">República Eslovaca</option>
                                  <option value="RE">Reunión</option>
                                  <option value="RW">Ruanda</option>
                                  <option value="RO">Rumania</option>
                                  <option value="RU">Rusia</option>
                                  <option value="EH">Sahara Occidental</option>
                                  <option value="KN">Saint Kitts y Nevis</option>
                                  <option value="WS">Samoa</option>
                                  <option value="AS">Samoa Americana</option>
                                  <option value="SM">San Marino</option>
                                  <option value="VC">San Vicente y Granadinas</option>
                                  <option value="SH">Santa Helena</option>
                                  <option value="LC">Santa Lucía</option>
                                  <option value="ST">Santo Tomé y Príncipe</option>
                                  <option value="SN">Senegal</option>
                                  <option value="SC">Seychelles</option>
                                  <option value="SL">Sierra Leona</option>
                                  <option value="SG">Singapur</option>
                                  <option value="SY">Siria</option>
                                  <option value="SO">Somalia</option>
                                  <option value="LK">Sri Lanka</option>
                                  <option value="PM">St. Pierre y Miquelon</option>
                                  <option value="SZ">Suazilandia</option>
                                  <option value="SD">Sudán</option>
                                  <option value="SE">Suecia</option>
                                  <option value="CH">Suiza</option>
                                  <option value="SR">Surinam</option>
                                  <option value="TH">Tailandia</option>
                                  <option value="TW">Taiwán</option>
                                  <option value="TZ">Tanzania</option>
                                  <option value="TJ">Tayikistán</option>
                                  <option value="TF">Territorios franceses del Sur</option>
                                  <option value="TP">Timor Oriental</option>
                                  <option value="TG">Togo</option>
                                  <option value="TO">Tonga</option>
                                  <option value="TT">Trinidad y Tobago</option>
                                  <option value="TN">Túnez</option>
                                  <option value="TM">Turkmenistán</option>
                                  <option value="TR">Turquía</option>
                                  <option value="TV">Tuvalu</option>
                                  <option value="UA">Ucrania</option>
                                  <option value="UG">Uganda</option>
                                  <option value="UY">Uruguay</option>
                                  <option value="UZ">Uzbekistán</option>
                                  <option value="VU">Vanuatu</option>
                                  <option value="VE">Venezuela</option>
                                  <option value="VN">Vietnam</option>
                                  <option value="YE">Yemen</option>
                                  <option value="YU">Yugoslavia</option>
                                  <option value="ZM">Zambia</option>
                                  <option value="ZW">Zimbawe</option>
                                </select>
                            </div>
                        </div>
                  {*      <div class="CZonaProvinciaLugarYFecha">
                            <div class="CLabelProvinciaLugarYFecha">Provincia:</div>
                            <div class="CComboProvinciaLugarYFecha">
                                <select name="selectProvincia">
                                    <option value="0">Seleccionar...</option>
                                    <option value="2">Álava</option>
                                    <option value="3">Albacete</option>
                                    <option value="4">Alicante/Alacant</option>
                                    <option value="5">Almería</option>
                                    <option value="6">Asturias</option>
                                    <option value="7">Ávila</option>
                                    <option value="8">Badajoz</option>
                                    <option value="9">Barcelona</option>
                                    <option value="10">Burgos</option>
                                    <option value="11">Cáceres</option>
                                    <option value="12">Cádiz</option>
                                    <option value="13">Cantabria</option>
                                    <option value="14">Castellón/Castelló</option>
                                    <option value="15">Ceuta</option>
                                    <option value="16">Ciudad Real</option>
                                    <option value="17">Córdoba</option>
                                    <option value="18">Cuenca</option>
                                    <option value="19">Girona</option>
                                    <option value="20">Las Palmas</option>
                                    <option value="21">Granada</option>
                                    <option value="22">Guadalajara</option>
                                    <option value="23">Guipúzcoa</option>
                                    <option value="24">Huelva</option>
                                    <option value="25">Huesca</option>
                                    <option value="26">Illes Balears</option>
                                    <option value="27">Jaén</option>
                                    <option value="28">A Coruña</option>
                                    <option value="29">La Rioja</option>
                                    <option value="30">León</option>
                                    <option value="31">Lleida</option>
                                    <option value="32">Lugo</option>
                                    <option value="33">Madrid</option>
                                    <option value="34">Málaga</option>
                                    <option value="35">Melilla</option>
                                    <option value="36">Murcia</option>
                                    <option value="37">Navarra</option>
                                    <option value="38">Ourense</option>
                                    <option value="39">Palencia</option>
                                    <option value="40">Pontevedra</option>
                                    <option value="41">Salamanca</option>
                                    <option value="42">Segovia</option>
                                    <option value="43">Sevilla</option>
                                    <option value="44">Soria</option>
                                    <option value="45">Tarragona</option>
                                    <option value="46">Santa Cruz de Tenerife</option>
                                    <option value="47">Teruel</option>
                                    <option value="48">Toledo</option>
                                    <option value="49">Valencia/Valéncia</option>
                                    <option value="50">Valladolid</option>
                                    <option value="51">Vizcaya</option>
                                    <option value="52">Zamora</option>
                                    <option value="53">Zaragoza</option>
                                </select>
                            </div>
                        </div>
			*}
                        <div class="CZonaLocalidadLugarYFecha">
                            <div class="CLabelLocalidadLugarYFecha">Localidad:</div>
                            <div class="CInputLocalidadLugarYFecha"><input type="text" name="localidad"></div>
                        </div>

                        <div class="CZonaFechaLugarYFecha">

                            <div class="CLabelFechaLugarYFecha">Fecha:</div>

                            <div class="CDiaLugarYFecha">
                                <select name="selectDia">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select>
                            </div>

                            <div class="CMesLugarYFecha">
                                <select name="selectMes">
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <div class="CAnyoLugarYFecha">
                                <select name="selectAnyo">
                                    <option value="2014">2014</option>
                                    <option value="2013">2013</option>
                                    <option value="2012">2012</option>
                                    <option value="2011">2011</option>
                                    <option value="2010">2010</option>
                                    <option value="2009" selected="selected">2009</option>
                                    <option value="2008">2008</option>
                                    <option value="2007">2007</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fileteHorizontalEnvioNota"></div>
                <div class="CPunto5EnvioNoticia">
                    <div class="CCabeceraFormulario1">
                        <span class="CDestacadoEnvioNoticia">
                            5. Condiciones
                        </span>
                    </div>
                    <div class="CFragmentoFormulario">
                        <div class="CContenedorRadioButton">
                            <div class="CTextoCondiciones">
                                <input type="radio" name="aceptarCondicones" class="validate-one-required" />
                                <span class="CTextoAceptoCond">Si Acepto</span>,
                                he leído y acepto todos los términos de las
                                <span class="CTextoGarantiasCond">
                                    <a href="/estaticas/aviso-legal/" target="_blank" title="Garantías de Usuario">Garantías de Usuario</a>
                                </span>
     
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fileteHorizontalEnvioNota"></div>
                <div class="CZonaBotonEnviarNoticia">
               		 <input type="hidden" name="action" value="send" />                   
                    {* <a onClick="document.envio.submit()" id="envio" style="cursor: pointer;"><img style="cursor: pointer;" alt="Enviar noticia" src="{$params.IMAGE_DIR}envio_noticia/botonEnviar.gif"/></a> *}
                    <input type="image" src="{$params.IMAGE_DIR}envio_noticia/botonEnviar.gif" style="cursor: pointer;" alt="Enviar noticia" />
                </div>
            </div>
        </div>
    </form>

{literal}
<script language="javascript" type="text/javascript">
/*<![CDATA[*/
var ConectaDynamicForm = Class.create({
    initialize: function() {
        var radios = $$('[name="tipoArchivo"]');
        for(var i=0; i<radios.length; i++) {
            radios[i].observe( 'click', this.selectChoice.bindAsEventListener(this) );
        }       
        
        // Load first option
        this.reloadTemasPrincipales( 1 );
    },
  
  /*  reloadTemasPrincipales: function(values) {
        var options = { 1: 'FOTO DEL DÍA',
                        2: 'FOTO DENUNCIAS',
                        3: 'VÍDEO DEL DÍA',
                        4: 'VÍDEO DENUNCIAS',
                        5: 'CARTAS AL DIRECTOR',
                        6: 'OPINIÓN DEL LECTOR' }
         
        var tp = $('temasPrincipales');
        
        tp.options.length = 0;
        for(var i=0; i<values.length; i++) {
            tp.options[ tp.options.length ] = new Option( options[values[i]], values[i], false, false );
        }
    },
*/

  reloadTemasPrincipales: function(values) {
{/literal}
      {section loop=$allcategorys name=t}
            {assign var=categorys value=$allcategorys[t]}
            {section loop=$categorys name=c}
                 {if $smarty.section.c.first}
                    var array_{$smarty.section.t.index} =[
                 {/if}
                 [ '{$categorys[c]->pk_content_category}','{$categorys[c]->title}']
                 {if $smarty.section.c.last}
                    ]
                 {else}
                    ,
                 {/if}
            {/section}

      {/section}
{literal}
 /* ejemplo array generado
        var array_1 = [  ['4','FOTO DEL vacas'],
                        ['2','FOTO DEL DÍA'],
                        ['3','FOTO DENUNCIAS']]

*/
        var tp = $('temasPrincipales');
       
        tp.options.length = 0;
        var array_name='array_'+values;
        var elarray = eval(array_name);
 
        for(var i=0; i<elarray.length; i++) {
            tp.options[ tp.options.length ] = new Option( elarray[i][1], elarray[i][0], false, false );
        }
    },

    selectChoice: function(event) {
        var element = Event.element(event);        
        switch( element.value ) {
            case '3': // CARTA
                $('div_AdjuntarArchivos').setStyle({display: 'none'});
                $('div_codigoVideo').setStyle({display: 'none'});
                
                this.reloadTemasPrincipales( 3 );
            break;
            
            case '4': // OPINION
                $('div_AdjuntarArchivos').setStyle({display: 'none'});
                $('div_codigoVideo').setStyle({display: 'none'});
                
                this.reloadTemasPrincipales( 4 );
            break;
            
            case '2': // VIDEO
                $('div_codigoVideo').setStyle({display: 'inline'});
                $('div_AdjuntarArchivos').setStyle({display: 'none'});
                
                this.reloadTemasPrincipales( 2 );
            break;
            
            case '1': // FOTO
            default:
                $('div_AdjuntarArchivos').setStyle({display: 'inline'});
                $('div_codigoVideo').setStyle({display: 'none'});
                
                this.reloadTemasPrincipales( 1 );
            break;
        }
    }  
});
    
function textCounter( field, countfield, maxlimit ){
    if ( field.value.length > maxlimit ) {
      field.value = field.value.substring( 0, maxlimit );
      alert( 'Tamaño máximo: No puede exceder de 2.000 carácteres.' );
      return false;
    } else {
      countfield.value = maxlimit - field.value.length;
    }
}




    new ConectaDynamicForm();
    new Validation('envio');  
/*]]>*/
</script>
{/literal}

{* http://ajax.googleapis.com/ajax/services/search/video?v=1.0&q=Paris%20Hilton *}
{* <script src="http://www.google.com/jsapi" type="text/javascript"></script>
<script language="Javascript" type="text/javascript">
//<![CDATA[
google.load('search', '1', {"language" : "es"});

function OnLoad() {    
    var searchControl = new google.search.SearchControl();
    searchControl.addSearcher( new google.search.VideoSearch() );
    //console.log(searchControl);
    
    searchControl.onSearchComplete = function() {
        //console.log(this);
    };
    //searchControl.setNoHtmlGeneration();
    /* searchControl.createResultHtml = function(result) {
        //console.log(result);
    }; */
    
    searchControl.draw( document.getElementById("youtubeSearchControl") );
}
google.setOnLoadCallback(OnLoad);
//]]>
</script> *}