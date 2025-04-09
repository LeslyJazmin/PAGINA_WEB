import sys

def verificar_librerias():
    print("Verificando librerías necesarias...\n")
    
    # Lista de librerías a verificar
    librerias = {
        'mysql.connector': 'mysql-connector-python',
        'qrcode': 'qrcode[pil]',
        'PIL': 'Pillow'
    }
    
    todas_instaladas = True
    
    for lib, pip_name in librerias.items():
        try:
            if lib == 'PIL':
                import PIL
                version = PIL.__version__
            elif lib == 'mysql.connector':
                import mysql.connector
                version = mysql.connector.__version__
            else:
                import qrcode
                version = qrcode.__version__
                
            print(f"✓ {lib} está instalada (versión {version})")
            
        except ImportError:
            todas_instaladas = False
            print(f"✗ {lib} no está instalada")
            print(f"  Instalar con: pip install {pip_name}")
    
    return todas_instaladas

if __name__ == "__main__":
    print(f"Python versión: {sys.version}\n")
    if verificar_librerias():
        print("\n¡Todas las librerías están instaladas correctamente!")
    else:
        print("\nFaltan algunas librerías. Por favor, instálalas usando los comandos indicados.")