
import React, { useState, useEffect } from 'react';
import './App.css';
import PrestamoModal from './components/PrestamoModal';
import PrestamosUsuarioModal from './components/PrestamosUsuarioModal';

function App() {
  const [view, setView] = useState('libros');
  const [libros, setLibros] = useState([]);
  const [usuarios, setUsuarios] = useState([]);
  const [showLibroModal, setShowLibroModal] = useState(false);
  const [showUsuarioModal, setShowUsuarioModal] = useState(false);
  const [showPrestamoModal, setShowPrestamoModal] = useState(false);
  const [showPrestamosUsuarioModal, setShowPrestamosUsuarioModal] = useState(false);
  const [currentLibro, setCurrentLibro] = useState(null);
  const [currentUsuario, setCurrentUsuario] = useState(null);
  const [selectedUsuario, setSelectedUsuario] = useState(null);
  const [prestamosUsuario, setPrestamosUsuario] = useState([]);
  const [searchParams, setSearchParams] = useState({
    termino: '',
    tipo: 'titulo'
  });
  const [libroForm, setLibroForm] = useState({
    titulo: '',
    autor: '',
    categoria: '',
    isbn: ''
  });
  const [usuarioForm, setUsuarioForm] = useState({
    nombre: '',
    email: '',
    tipo: 'estudiante'
  });

  useEffect(() => {
    fetchLibros();
    fetchUsuarios();
  }, []);

  // Funciones de fetch
  const fetchLibros = async () => {
    try {
      const response = await fetch('http://localhost:8000/libros');
      const data = await response.json();
      if (data.status === 'success') {
        setLibros(data.data || []);
      }
    } catch (error) {
      console.error('Error:', error);
    }
  };

  const fetchUsuarios = async () => {
    try {
      const response = await fetch('http://localhost:8000/usuarios');
      const data = await response.json();
      if (data.status === 'success') {
        setUsuarios(data.data || []);
      }
    } catch (error) {
      console.error('Error:', error);
    }
  };

  // Funciones para libros
  const handleLibroSubmit = async (e) => {
    e.preventDefault();
    try {
      const url = currentLibro 
        ? `http://localhost:8000/libros/${currentLibro.id}`
        : 'http://localhost:8000/libros';
      
      const method = currentLibro ? 'PUT' : 'POST';
      
      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(libroForm)
      });

      if (response.ok) {
        setShowLibroModal(false);
        setLibroForm({ titulo: '', autor: '', categoria: '', isbn: '' });
        setCurrentLibro(null);
        fetchLibros();
      }
    } catch (error) {
      console.error('Error:', error);
    }
  };

  const handleSearch = async (e) => {
    e.preventDefault();
    if (searchParams.termino) {
      try {
        const response = await fetch(`http://localhost:8000/libros?search=${searchParams.termino}&type=${searchParams.tipo}`);
        const data = await response.json();
        if (data.status === 'success') {
          setLibros(data.data);
        }
      } catch (error) {
        console.error('Error:', error);
      }
    } else {
      fetchLibros();
    }
  };

  const eliminarLibro = async (id) => {
    if (window.confirm('¿Estás seguro de eliminar este libro?')) {
      try {
        const response = await fetch(`http://localhost:8000/libros/${id}`, {
          method: 'DELETE'
        });
        if (response.ok) {
          fetchLibros();
        }
      } catch (error) {
        console.error('Error:', error);
      }
    }
  };

  // Funciones para usuarios
  const handleUsuarioSubmit = async (e) => {
    e.preventDefault();
    try {
      const url = currentUsuario 
        ? `http://localhost:8000/usuarios/${currentUsuario.id}`
        : 'http://localhost:8000/usuarios';
      
      const method = currentUsuario ? 'PUT' : 'POST';
      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(usuarioForm)
      });
  
      const data = await response.json();
      
      if (data.status === 'success') {
        setShowUsuarioModal(false);
        setUsuarioForm({ nombre: '', email: '', tipo: 'estudiante' });
        setCurrentUsuario(null);
        fetchUsuarios();
        alert(data.message);
      } else {
        alert(data.message || 'Error al procesar la solicitud');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error al procesar la solicitud');
    }
  };
  const eliminarUsuario = async (id) => {
    if (window.confirm('¿Estás seguro de eliminar este usuario?')) {
      try {
        const response = await fetch(`http://localhost:8000/usuarios/${id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json'
          }
        });
  
        const data = await response.json();
        
        if (data.status === 'success') {
          fetchUsuarios();
          alert(data.message);
        } else {
          alert(data.message || 'Error al eliminar el usuario');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar el usuario');
      }
    }
  };

  // Funciones para préstamos
  const handlePrestamo = async (libroId, usuarioId) => {
    try {
      console.log('Creando préstamo:', { libro_id: libroId, usuario_id: usuarioId });
      const response = await fetch('http://localhost:8000/libros/prestar', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          libro_id: libroId,
          usuario_id: usuarioId
        })
      });
  
      const data = await response.json();
      console.log('Respuesta del servidor:', data);
  
      if (data.status === 'success') {
        setShowPrestamoModal(false);
        fetchLibros();
        alert('Préstamo realizado con éxito');
      } else {
        alert('Error al realizar el préstamo: ' + data.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error al realizar el préstamo');
    }
  };
  const handleDevolverLibro = async (libroId) => {
    try {
      const response = await fetch(`http://localhost:8000/libros/devolver`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ libro_id: libroId })
      });

      if (response.ok) {
        fetchLibros();
        alert('Libro devuelto con éxito');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error al devolver el libro');
    }
  };

  const verPrestamosPorUsuario = async (usuarioId) => {
    try {
      console.log('Consultando préstamos para usuario:', usuarioId);
      const response = await fetch(`http://localhost:8000/usuarios/${usuarioId}/prestamos`);
      const data = await response.json();
      console.log('Respuesta de préstamos:', data);
  
      if (data.status === 'success') {
        setPrestamosUsuario(data.data);
        setSelectedUsuario(usuarios.find(u => u.id === usuarioId));
        setShowPrestamosUsuarioModal(true);
      } else {
        alert('Error al obtener los préstamos: ' + data.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Error al obtener los préstamos');
    }
  };
  return (
    <div className="min-h-screen bg-gray-100">
      <header className="bg-blue-600 text-white shadow-lg">
        <div className="container mx-auto px-4 py-6">
          <div className="flex justify-between items-center">
            <h1 className="text-3xl font-bold">Sistema de Biblioteca</h1>
            <nav className="space-x-4">
              <button 
                className={`px-4 py-2 rounded-lg ${view === 'libros' ? 'bg-blue-700' : 'hover:bg-blue-700'}`}
                onClick={() => setView('libros')}
              >
                Libros
              </button>
              <button 
                className={`px-4 py-2 rounded-lg ${view === 'usuarios' ? 'bg-blue-700' : 'hover:bg-blue-700'}`}
                onClick={() => setView('usuarios')}
              >
                Usuarios
              </button>
            </nav>
          </div>
        </div>
      </header>

      <main className="container mx-auto px-4 py-8">
        {view === 'libros' ? (
          <>
            {/* Búsqueda y botón agregar libros */}
            <div className="flex justify-between mb-6">
              <form onSubmit={handleSearch} className="flex gap-4 flex-1 mr-4">
                <select
                  className="border rounded-lg px-4 py-2"
                  value={searchParams.tipo}
                  onChange={(e) => setSearchParams({...searchParams, tipo: e.target.value})}
                >
                  <option value="titulo">Título</option>
                  <option value="autor">Autor</option>
                  <option value="categoria">Categoría</option>
                </select>
                <input
                  type="text"
                  placeholder="Buscar libros..."
                  className="flex-1 border rounded-lg px-4 py-2"
                  value={searchParams.termino}
                  onChange={(e) => setSearchParams({...searchParams, termino: e.target.value})}
                />
                <button 
                  type="submit"
                  className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
                >
                  Buscar
                </button>
              </form>
              <button
                onClick={() => {
                  setCurrentLibro(null);
                  setLibroForm({ titulo: '', autor: '', categoria: '', isbn: '' });
                  setShowLibroModal(true);
                }}
                className="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700"
              >
                Agregar Libro
              </button>
            </div>

            {/* Tabla de libros */}
            <div className="bg-white rounded-lg shadow-md overflow-hidden">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Autor</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ISBN</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {libros.map((libro) => (
                    <tr key={libro.id}>
                      <td className="px-6 py-4 whitespace-nowrap">{libro.titulo}</td>
                      <td className="px-6 py-4 whitespace-nowrap">{libro.autor}</td>
                      <td className="px-6 py-4 whitespace-nowrap">{libro.categoria}</td>
                      <td className="px-6 py-4 whitespace-nowrap">{libro.isbn}</td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`px-2 py-1 rounded-full text-xs ${
                          libro.disponible === "1" 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-red-100 text-red-800'
                        }`}>
                          {libro.disponible === "1" ? 'Disponible' : 'Prestado'}
                        </span>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap space-x-2">
                        <button
                          onClick={() => {
                            setCurrentLibro(libro);
                            setLibroForm(libro);
                            setShowLibroModal(true);
                          }}
                          className="text-blue-600 hover:text-blue-900"
                        >
                          Editar
                        </button>
                        <button
                          onClick={() => eliminarLibro(libro.id)}
                          className="text-red-600 hover:text-red-900"
                        >
                          Eliminar
                        </button>
                        {libro.disponible === "1" ? (
                          <button 
                            onClick={() => {
                              setCurrentLibro(libro);
                              setShowPrestamoModal(true);
                            }}
                            className="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700"
                          >
                            Prestar
                          </button>
                        ) : (
                          <button
                            onClick={() => handleDevolverLibro(libro.id)}
                            className="bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700"
                          >
                            Devolver
                          </button>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        ) : (
          <>
            {/* Vista de usuarios */}
            <div className="flex justify-end mb-6">
              <button
                onClick={() => {
                  setCurrentUsuario(null);
                  setUsuarioForm({ nombre: '', email: '', tipo: 'estudiante' });
                  setShowUsuarioModal(true);
                }}
                className="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700"
              >
                Agregar Usuario
              </button>
            </div>

            <div className="bg-white rounded-lg shadow-md overflow-hidden">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Préstamos Actuales
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {usuarios.map((usuario) => (
                    <tr key={usuario.id}>
                      <td className="px-6 py-4 whitespace-nowrap">{usuario.nombre}</td>
                      <td className="px-6 py-4 whitespace-nowrap">{usuario.email}</td>
                      <td className="px-6 py-4 whitespace-nowrap">{usuario.tipo}</td>
                      <td className="px-6 py-4 whitespace-nowrap">{usuario.prestamos_actuales || 0}</td>
                      <td className="px-6 py-4 whitespace-nowrap space-x-2">
                        <button
                          onClick={() => {
                            setCurrentUsuario(usuario);
                            setUsuarioForm(usuario);
                            setShowUsuarioModal(true);
                          }}
                          className="text-blue-600 hover:text-blue-900"
                        >
                          Editar
                        </button>
                        <button
                          onClick={() => eliminarUsuario(usuario.id)}
                          className="text-red-600 hover:text-red-900"
                        >
                          Eliminar
                        </button>
                        <button
                          onClick={() => {
                            setSelectedUsuario(usuario);
                            verPrestamosPorUsuario(usuario.id);
                          }}
                          className="text-green-600 hover:text-green-900"
                        >
                          Ver Préstamos
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </>
        )}
      </main>

      {/* Modal Libro */}
      {showLibroModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-lg p-8 max-w-md w-full">
            <h2 className="text-2xl font-bold mb-6">
              {currentLibro ? 'Editar Libro' : 'Agregar Libro'}
            </h2>
            <form onSubmit={handleLibroSubmit} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Título
                </label>
                <input
                  type="text"
                  name="titulo"
                  value={libroForm.titulo}
                  onChange={(e) => setLibroForm({...libroForm, titulo: e.target.value})}
                  className="w-full border rounded-lg px-4 py-2"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Autor
                </label>
                <input
                  type="text"
                  name="autor"
                  value={libroForm.autor}
                  onChange={(e) => setLibroForm({...libroForm, autor: e.target.value})}
                  className="w-full border rounded-lg px-4 py-2"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Categoría
                </label>
                <input
                  type="text"
                  name="categoria"
                  value={libroForm.categoria}
                  onChange={(e) => setLibroForm({...libroForm, categoria: e.target.value})}
                  className="w-full border rounded-lg px-4 py-2"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  ISBN
                </label>
                <input
                  type="text"
                  name="isbn"
                  value={libroForm.isbn}
                  onChange={(e) => setLibroForm({...libroForm, isbn: e.target.value})}
                  className="w-full border rounded-lg px-4 py-2"
                  required
                />
              </div>
              <div className="flex justify-end gap-4">
                <button
                  type="button"
                  onClick={() => setShowLibroModal(false)}
                  className="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                >
                  {currentLibro ? 'Actualizar' : 'Guardar'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal Usuario */}
      {showUsuarioModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-lg p-8 max-w-md w-full">
            <h2 className="text-2xl font-bold mb-6">
              {currentUsuario ? 'Editar Usuario' : 'Agregar Usuario'}
            </h2>
            <form onSubmit={handleUsuarioSubmit} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Nombre
                </label>
                <input
                  type="text"
                  name="nombre"
                  value={usuarioForm.nombre}
                  onChange={(e) => setUsuarioForm({...usuarioForm, nombre: e.target.value})}
                  className="w-full border rounded-lg px-4 py-2"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Email
                </label>
                <input
                  type="email"
                  name="email"
                  value={usuarioForm.email}
                  onChange={(e) => setUsuarioForm({...usuarioForm, email: e.target.value})}
                  className="w-full border rounded-lg px-4 py-2"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Tipo
                </label>
                <select
                  name="tipo"
                  value={usuarioForm.tipo}
                  onChange={(e) => setUsuarioForm({...usuarioForm, tipo: e.target.value})}
                  className="w-full border rounded-lg px-4 py-2"
                  required
                >
                  <option value="estudiante">Estudiante</option>
                  <option value="profesor">Profesor</option>
                </select>
              </div>
              <div className="flex justify-end gap-4">
                <button
                  type="button"
                  onClick={() => setShowUsuarioModal(false)}
                  className="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                >
                  {currentUsuario ? 'Actualizar' : 'Guardar'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modales de Préstamos */}
      {showPrestamoModal && currentLibro && (
        <PrestamoModal
          libro={currentLibro}
          usuarios={usuarios}
          onConfirm={(usuarioId) => handlePrestamo(currentLibro.id, usuarioId)}
          onClose={() => setShowPrestamoModal(false)}
        />
      )}

      {showPrestamosUsuarioModal && selectedUsuario && (
        <PrestamosUsuarioModal
          usuario={selectedUsuario}
          prestamos={prestamosUsuario}
          onClose={() => setShowPrestamosUsuarioModal(false)}
        />
      )}
    </div>
  );
}

export default App;