package jot.java;
import java.util.*;
public class ff{
    public static void main(String[]args){
        Collection<Integer> l= new ArrayList<>();
        l.add(1);
        l.add(2);
        l.add(3);
        l.forEach(n -> { System.out.println(n); });
    }
}