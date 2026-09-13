/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package studentmanagementsystem;

import java.util.ArrayList;

/**
 *
 * @author munei
 */
class StudentManager {
    private final ArrayList<Student> students = new ArrayList<>();

    // Add student
    public void addStudent(Student student) {
        students.add(student);
        System.out.println("Student added successfully!");
    }

    // Display all students
    public void displayStudents() {
        if (students.isEmpty()) {
            System.out.println("No students available.");
        } else {
            for (Student s : students) {
                System.out.println(s);
            }
        }
    }

    // Search student by ID
    public Student searchStudent(String id) {
        for (Student s : students) {
            if (s.getStudentID().equalsIgnoreCase(id)) {
                return s;
            }
        }
        return null;
    }

    // Remove student by ID
    public boolean removeStudent(String id) {
        Student s = searchStudent(id);
        if (s != null) {
            students.remove(s);
            return true;
        }
        return false;
    }

    // Update student details
    public boolean updateStudent(String id, String newName, int newAge) {
        Student s = searchStudent(id);
        if (s != null) {
            s.setName(newName);
            s.setAge(newAge);
            return true;
        }
        return false;
    }

}

