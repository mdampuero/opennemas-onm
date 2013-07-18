La region internacional import script
=====================================

Tables
------

 - centros: list of galician inmigration centers
 - Columna: list of opinions with reference to author
 - Elementos: multimedia elements (foto, video)
 - Elementos_Viejos: same as the previous element
 - Galerias
 - Galerias_Foto
 - Noticias
 - Noticias_Categorias
 - Noticias_Viejas


Preparing the old database
--------------------------

As there are two duplicated tables ("Viej@s" tables), in order to simplify our
importation we will merge those duplicated tables.

SET foreign_key_checks = 0;

INSERT INTO Elementos SELECT * FROM Elementos_Viejos;
DROP TABLE Elementos_Viejos;

INSERT INTO Noticias SELECT * FROM Noticias_Viejas;
DROP TABLE Noticias_Viejas;

Database schema
---------------

- `Columna`
    . idColumna     : opinion id
    . idUsuario     : creator of this opinion
    . Fecha         : creation date: timestamp
    . Titulo        : title for this opinion
    . Contenido     : body for this opinion
- `Elementos`
    . idElemento    : id of this multimedia
    . idNoticia     : id of the article that is related this image
    . Tipo          : foto, video, video-efe, destacado
    . Archivo       : relative path to the file in the server (/imagenes/elemento/{Archivo})
    . Enlace        : quite weird: videos have a "code" maybe form Youtube, fotos has same content as Pie
    . Alt           : photos only: same congent as Pie
    . Pie           : photos only: description of this content
    . Peso          : maybe some kind of flag for ordering
    . Nombre        : Title of this content
    . Fecha         : timestamp of creation (some are empty)
- `Galerias`
    . idGalerias    : id of this gallery
    . Nombre        : title of this gallery
    . idNoticia     : not used, all are set to 0
    . Descripcion   : body of this gallery
    . DestacadaPortada : not relevant
    . Peso          : not relevant
    . idUsuario     : id of the user who have created this element
    . Fecha         : creation date
- `Galerias_Foto`
    . idFoto        : id of the foto, not related with elementos
    . idGaleria     : id of gallery that this element is related to
    . Titulo        : title of the photo
    . Descarga      : relative path to the file in the server (/imagenes/elemento/{Descarga})
    . Descripcion   : body of this foto
    . Peso          : not relevant
    . Puntuacion    : not relevant
    . Visitas       : number of visits
- `Noticias`
    . idNoticias    : article id
    . Titulo        : title
    . Antetitulo    : pretitle
    . Entradilla    : subtitle
    . Contenido     : summary
    . Keywords      : metadata
    . HoraPublicacion : starttime timestamp
    . HoraAlta      : create timestamp
    . Publicada     : boolean
    . DestacadaPortada : not relevant
    . DestacadaSeccion : not relevant
    . Idioma        : not relevant
    . Fuente        : signature
    . Ciudad        : not relevant
    . Categoria     : id
    . Categoria2    : not relevant
    . idUsuario     : author of this article
    . Visitas       : views
    . Puntuacion    : not relevant
    . Orden         : not relevant
    . OrdenPortada  : not relevant
    . noindex       : not relevant
- `Noticias_Categorias`
    . idNoticias_Categorias : category id
    . idPadre       : father category
    . Nombre        : name
    . Descripcion   : description
    . DestacadaPortada : not relevant
    . Peso          : not relevant
    . Actualizacion : not relevant
    . TempMax       : not relevant
- `Usuarios`
    . idUsuario     : id of the user
    . Nombre        : name of the user
    . Foto          : relative path to the image (images/autores/{Foto})

Migration
=========
- centros => do not migrate
- Columna => opinions table
    . idColumn          : opinion->id
    . idUsuario         : opinion->pk_author
    . Fecha             : opinion->created, convert to datetime from timestamp.
    . Titulo            : opinion->title
    . Contenido         : opinion->body

- Elementos => photos, videos

    if Tipo == video-efe OR Tipo == destacado
        drop this element
    else if Elementos == video
        Do not import elements that Enlace contains video.google.com or some HTML code
        SELECT *  FROM  `Elementos`  WHERE  `Tipo` LIKE  'video' AND  `Enlace` NOT LIKE  '%video.google.com%' AND  `Enlace` NOT LIKE  '%<object%'
        . idElemento    : Video->id
        . Enlace        : Video->video_url = http://youtube.com/watch?v={Enlace}
        . Pie           : Video->description
        . Nombre        : Title of this content
        . Fecha         : timestamp of creation (some are empty)
    else if Tipo == foto
        . idElemento    : Photo->id
        . Archivo       : Photo->local_file (imagenes/elementos/{Archivo})
        . Pie           : Photo->summary
        . Nombre        : Photo->title
        . Fecha         : convert to datetime from timestamp. contents.created

- Noticias


- `Noticias_Categorias` => content_categories
    . idNoticias_Categorias : content_categories.pk_content_category
    . idPadre       : content_categories.fk_content_category
    . Nombre        : content_categories.title
    . Descripcion   : content_categories.params['description']


Migration STATUS
================
- Categories DONE
- Articles
- Images
- Images-Articles
- Videos
- Authors
- Opinions

URL Rewriting
=============

http://www.laregioninternacional.com/noticia/{article_id}/.* inner article
http://www.laregioninternacional.com/galeria/{gallery_id}/.* inner album
http://www.laregioninternacional.com/opinion/{opinion_id}/ opinion inner, but raises an 404 error
http://www.laregioninternacional.com/opinion.php?p=34 -> opinion frontpage
http://www.laregioninternacional.com/videos.php?p=34 -> video frontpage
http://www.laregioninternacional.com/galerias.php -> album frontpage
